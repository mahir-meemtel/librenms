<?php
namespace ObzoraNMS\Util;

use Illuminate\Support\Str;
use ObzoraNMS\Exceptions\UserFunctionExistException;

class UserFuncHelper
{
    public function __construct(
        public string|int|float $value,
        public string|int|float|null $value_raw = null,
        public array $sensor = [],
    ) {
    }

    public function __call(string $name, array $arguments): mixed
    {
        throw new UserFunctionExistException("Invalid user function: $name");
    }

    public function dateToRuntime(): int
    {
        return Time::dateToMinutes($this->value_raw);
    }

    public function fsParseChannelValue(): float
    {
        $channel = Str::afterLast($this->sensor['sensor_index'], '.');

        return Number::cast(explode(',', $this->value_raw)[$channel] ?? '') * $this->sensor['sensor_multiplier'] / $this->sensor['sensor_divisor'];
    }

    public function hhmmssToMinutes(): int
    {
        [$h, $m, $s] = explode(':', $this->value_raw);

        return (int) ((int) $h * 60 + (int) $m + (int) $s / 60);
    }
}
