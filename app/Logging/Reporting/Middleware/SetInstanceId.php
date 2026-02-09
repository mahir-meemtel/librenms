<?php
namespace App\Logging\Reporting\Middleware;

use App\Models\Callback;
use Illuminate\Support\Str;
use Spatie\FlareClient\Report;

class SetInstanceId implements \Spatie\FlareClient\FlareMiddleware\FlareMiddleware
{
    private static ?string $instanceId = null;

    /**
     * Middleware to add instance ID, piggybacks on the "user id" feature.
     *
     * @return mixed
     */
    public function handle(Report $report, \Closure $next)
    {
        try {
            $user = $report->getGroup('user', []);
            $user['id'] = self::getInstanceId();

            $report->group('user', $user);
        } catch (\Exception $e) {
        }

        return $next($report);
    }

    public static function getInstanceId(): string
    {
        if (is_null(self::$instanceId)) {
            $uuid = Callback::get('error_reporting_uuid');

            if (! $uuid) {
                $uuid = Str::uuid();
                Callback::set('error_reporting_uuid', $uuid);
            }

            self::$instanceId = $uuid;
        }

        return self::$instanceId;
    }
}
