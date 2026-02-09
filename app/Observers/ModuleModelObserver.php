<?php
namespace App\Observers;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Util\Debug;
use Psr\Log\LoggerInterface;

class ModuleModelObserver
{
    private LoggerInterface $logger;

    public function __construct(
        ?Logger $logger = null,
    ) {
        $this->logger = $logger ?? Log::channel('stdout');
    }

    /**
     * Install observers to output +, -, U for models being created, deleted, and updated
     *
     * @param  string|Eloquent  $model  The model name including namespace
     */
    public static function observe($model, string $name = ''): void
    {
        static $observed_models = []; // keep track of observed models so we don't duplicate output
        $class = ltrim($model, '\\');

        if ($name) {
            Log::channel('stdout')->info(ucwords($name) . ': ', ['nlb' => true]);
        }

        if (! in_array($class, $observed_models)) {
            $model::observe(new static());
            $observed_models[] = $class;
        }
    }

    public static function done(): void
    {
        Log::channel('stdout')->info(PHP_EOL, ['nlb' => true]);
    }

    /**
     * @param  Eloquent  $model
     */
    public function saving($model): void
    {
        if (! $model->isDirty()) {
            $this->logger->info('.', ['nlb' => true]);
        }
    }

    /**
     * @param  Eloquent  $model
     */
    public function updated($model): void
    {
        if (Debug::isEnabled()) {
            $this->logger->debug('Updated data:   ' . var_export($model->getDirty(), true));
        } else {
            $this->logger->info('U', ['nlb' => true]);
        }
    }

    /**
     * @param  Eloquent  $model
     */
    public function restored($model): void
    {
        if (Debug::isEnabled()) {
            $this->logger->debug('Restored data:   ' . var_export($model->getDirty(), true));
        } else {
            $this->logger->info('R', ['nlb' => true]);
        }
    }

    /**
     * @param  Eloquent  $model
     */
    public function created($model): void
    {
        $this->logger->info('+', ['nlb' => true]);
    }

    /**
     * @param  Eloquent  $model
     */
    public function deleted($model): void
    {
        $this->logger->info('-', ['nlb' => true]);
    }
}
