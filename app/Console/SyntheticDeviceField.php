<?php
namespace App\Console;

use App\Models\Device;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class SyntheticDeviceField
{
    public function __construct(
    public readonly string $name,
    public readonly array $columns = [],
    public readonly ?Closure $displayFunction = null,
    public readonly ?Closure $modifyQuery = null,
    public readonly ?string $headerName = null,
) {
    }

    public function headerName(): string
    {
        return $this->headerName ?? $this->name;
    }

    public function modifyQuery(Builder $query): Builder
    {
        if ($this->modifyQuery) {
            return call_user_func($this->modifyQuery, $query);
        }

        return $query;
    }

    public function toString(Device $device): string
    {
        if ($this->displayFunction) {
            return (string) call_user_func($this->displayFunction, $device);
        }

        return (string) $device->getAttributeValue($this->name);
    }
}
