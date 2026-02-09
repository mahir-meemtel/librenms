<?php
namespace App\Polling\Measure;

class Measurement
{
    private $start;
    private $type;
    private $duration;

    private function __construct(string $type, ?float $duration = null)
    {
        $this->type = $type;
        $this->start = microtime(true);
        if ($duration !== null) {
            $this->duration = $duration;
        }
    }

    /**
     * Create a measurement with an existing duration
     */
    public static function make(string $type, float $duration): Measurement
    {
        return new static($type, $duration);
    }

    /**
     * Start the timer for a new operation
     *
     * @param  string  $type
     * @return static
     */
    public static function start(string $type): Measurement
    {
        return new static($type);
    }

    /**
     * End the timer for this operation
     */
    public function end(): Measurement
    {
        $this->duration = microtime(true) - $this->start;

        return $this;
    }

    /**
     * Get the duration of the operation
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * Get the type of the operation
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function manager(): MeasurementManager
    {
        return app(MeasurementManager::class);
    }
}
