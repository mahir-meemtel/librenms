<?php
namespace App\Polling\Measure;

class MeasurementSummary
{
    private $type;
    private $count = 0;
    private $duration = 0.0;

    private $checkpointCount = 0;
    private $checkpointDuration = 0.0;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function add(Measurement $measurement): void
    {
        $this->count++;
        $this->duration += $measurement->getDuration();
    }

    /**
     * Get the measurement summary
     * ['count' => #, 'duration' => s]
     */
    public function get(): array
    {
        return [
            'count' => $this->count,
            'duration' => $this->duration,
        ];
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * Set a new checkpoint to compare against with diff methods
     */
    public function checkpoint(): void
    {
        $this->checkpointCount = $this->count;
        $this->checkpointDuration = $this->duration;
    }

    public function getCountDiff(): int
    {
        return $this->count - $this->checkpointCount;
    }

    public function getDurationDiff(): float
    {
        return $this->duration - $this->checkpointDuration;
    }
}
