<?php
namespace ObzoraNMS\Polling;

class Result
{
    private int $attempted = 0;
    private int $completed = 0;

    public function markAttempted(): void
    {
        $this->attempted++;
    }

    public function markCompleted(bool $success = true): void
    {
        if ($success) {
            $this->completed++;
        }
    }

    public function hasNoAttempts(): bool
    {
        return $this->attempted == 0;
    }

    public function hasNoCompleted(): bool
    {
        return $this->completed == 0;
    }

    public function hasAnyCompleted(): bool
    {
        return $this->completed > 0;
    }

    public function hasMultipleCompleted(): bool
    {
        return $this->completed > 1;
    }

    public function getCompleted(): int
    {
        return $this->completed;
    }

    public function getAttempted(): int
    {
        return $this->attempted;
    }
}
