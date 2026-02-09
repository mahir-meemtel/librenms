<?php
namespace App\Exceptions;

use App\Facades\ObzoraConfig;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use ObzoraNMS\Util\Git;
use Spatie\LaravelIgnition\Facades\Flare;
use Throwable;

class ErrorReporting
{
    private ?bool $reportingEnabled = null;
    protected array $upgradable = [
        \ObzoraNMS\Exceptions\FilePermissionsException::class,
        \ObzoraNMS\Exceptions\DatabaseConnectException::class,
        \ObzoraNMS\Exceptions\DuskUnsafeException::class,
        \ObzoraNMS\Exceptions\UnserializableRouteCache::class,
        \ObzoraNMS\Exceptions\MaximumExecutionTimeExceeded::class,
        \ObzoraNMS\Exceptions\DatabaseInconsistentException::class,
    ];

    public function __construct(Exceptions $exceptions)
    {
        $this->adjustErrorHandlingForAppEnv(app()->environment());

        $exceptions->dontReportDuplicates();
        $exceptions->throttle(fn (Throwable $e) => Limit::perMinute(ObzoraConfig::get('reporting.throttle', 30)));
        $exceptions->reportable([$this, 'reportable']);
        $exceptions->report([$this, 'report']);
        $exceptions->render([$this, 'render']);

        Flare::determineVersionUsing(function () {
            return \ObzoraNMS\Util\Version::VERSION;
        });
    }

    public function reportable(Throwable $e): bool
    {
        \Log::critical('%RException: ' . get_class($e) . ' ' . $e->getMessage() . '%n @ %G' . $e->getFile() . ':' . $e->getLine() . '%n' . PHP_EOL . $e->getTraceAsString(), ['color' => true]);

        return false; // false = block default log message
    }

    public function report(Throwable $e): bool
    {
        if ($this->isReportingEnabled()) {
            Flare::report($e);
        }

        return true;
    }

    public function render(Throwable $exception, Request $request): ?Response
    {
        // try to upgrade generic exceptions to more specific ones
        if (! config('app.debug')) {
            if ($exception instanceof \Illuminate\View\ViewException || $exception instanceof \Spatie\LaravelIgnition\Exceptions\ViewException) {
                $base = $exception->getPrevious(); // get real exception
            }

            foreach ($this->upgradable as $class) {
                if ($new = $class::upgrade($base ?? $exception)) {
                    return $new->render($request);
                }
            }
        }

        return null; // use default rendering
    }

    /**
     * Checks the state of the config and current install to determine if reporting should be enabled
     * The primary factor is the setting reporting.error
     */
    public function isReportingEnabled(): bool
    {
        if ($this->reportingEnabled !== null) {
            return $this->reportingEnabled;
        }

        // safety check so we don't leak early reports (but reporting should not be loaded before the config is)
        if (! app()->bound('obzora-config')) {
            return false;
        }

        $this->reportingEnabled = false; // don't cache before config is loaded

        // check the user setting
        if (ObzoraConfig::get('reporting.error') !== true) {
            \Log::debug('Reporting disabled by user setting');

            return false;
        }

        // Only run in production
        if (! app()->isProduction()) {
            \Log::debug('Reporting disabled because app is not in production mode');

            return false;
        }

        // Check git
        $git = Git::make(180);
        if ($git->isAvailable()) {
            if (! Str::contains($git->remoteUrl(), ['git@github.com:obzora/obzora.git', 'https://github.com/obzora/obzora.git'])) {
                \Log::debug('Reporting disabled because ObzoraNMS is not from the official repository');

                return false;
            }

            if ($git->hasChanges()) {
                \Log::debug('Reporting disabled because ObzoraNMS is not from the official repository');

                return false;
            }

            if (! $git->isOfficialCommits()) {
                \Log::debug('Reporting disabled due to local modifications');

                return false;
            }
        }

        $this->reportingEnabled = true;

        return true;
    }

    private function adjustErrorHandlingForAppEnv(string $environment): void
    {
        // throw exceptions and deprecations in testing and non-prod when APP_DEBUG is set.
        if ($environment == 'testing' || ($environment !== 'production' && config('app.debug'))) {
            app()->booted(function () {
                config([
                    'logging.deprecations.channel' => 'deprecations_channel',
                    'logging.deprecations.trace' => true,
                ]);
            });

            return; // do not override error handler below
        }

        // in production, don't halt execution on non-fatal errors
        set_error_handler(function ($severity, $message, $file, $line) {
            // If file is from a package, find the first non-vendor frame
            if (self::isUndesirableTracePath($file)) {
                // add vendor file to message
                $message .= ' from ' . strstr($file, 'vendor') . ':' . $line;
                [$file, $line] = self::findFirstNonVendorFrame();
            }

            if ((error_reporting() & $severity) !== 0) { // this check primarily allows @ to suppress errors
                error_log("\e[31mPHP Error($severity)\e[0m: $message in $file:$line");
            }

            // For notices and warnings, prevent conversion to exceptions
            if (($severity & (E_NOTICE | E_WARNING | E_USER_NOTICE | E_USER_WARNING | E_DEPRECATED)) !== 0) {
                return true; // Prevent the standard error handler from running
            }

            return false; // For other errors, let Laravel handle them
        });
    }

    private static function findFirstNonVendorFrame(): array
    {
        foreach (debug_backtrace() as $trace) {
            // not vendor frames
            if (isset($trace['file']) && self::isUndesirableTracePath($trace['file'])) {
                continue;
            }
            // not this class
            if (isset($trace['class']) && $trace['class'] === self::class) {
                continue;
            }

            return [$trace['file'], $trace['line']];
        }

        return ['', ''];
    }

    private static function isUndesirableTracePath(string $path): bool
    {
        return Str::contains($path, [
            '/vendor/',
            '/storage/framework/views/',
        ]);
    }
}
