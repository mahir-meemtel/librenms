<?php
namespace App\Polling\Measure;

use Illuminate\Support\Collection;

class MeasurementCollection extends Collection
{
    public function getTotalCount(): int
    {
        return $this->sumStat('getCount');
    }

    public function getTotalDuration(): float
    {
        return $this->sumStat('getDuration');
    }

    public function getCountDiff(): int
    {
        return $this->sumStat('getCountDiff');
    }

    public function getDurationDiff(): float
    {
        return $this->sumStat('getDurationDiff');
    }

    public function checkpoint(): void
    {
        $this->each->checkpoint();
    }

    public function record(Measurement $measurement): void
    {
        $type = $measurement->getType();

        if (! $this->has($type)) {
            $this->put($type, new MeasurementSummary($type));
        }

        $this->get($type)->add($measurement);
    }

    public function getSummary(string $type): MeasurementSummary
    {
        return $this->get($type, new MeasurementSummary($type));
    }

    /**
     * @param  string  $method  method on measurement class to call
     * @return int|float
     */
    private function sumStat(string $method)
    {
        return $this->reduce(function ($sum, $measurement) use ($method) {
            $sum += $measurement->$method();

            return $sum;
        }, 0);
    }
}
