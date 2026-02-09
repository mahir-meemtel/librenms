<?php
namespace App\Models;

use App\Facades\ObzoraConfig;
use App\Models\Traits\HasThresholds;
use Illuminate\Support\Arr;
use ObzoraNMS\Interfaces\Models\Keyable;
use ObzoraNMS\Util\Number;

class WirelessSensor extends DeviceRelatedModel implements Keyable
{
    use HasThresholds;

    const CREATED_AT = null;
    const UPDATED_AT = 'lastupdate';
    protected $primaryKey = 'sensor_id';
    protected $fillable = [
        'sensor_class',
        'sensor_index',
        'sensor_type',
        'sensor_descr',
        'sensor_divisor',
        'sensor_multiplier',
        'sensor_aggregator',
        'sensor_current',
        'sensor_prev',
        'sensor_limit',
        'sensor_limit_warn',
        'sensor_limit_low',
        'sensor_limit_low_warn',
        'sensor_alert',
        'sensor_custom',
        'entPhysicalIndex',
        'entPhysicalIndex_measured',
        'lastupdate',
        'sensor_oids',
        'access_point_id',
        'rrd_type',
    ];

    /**
     * @return array{sensor_oids: 'array'}
     */
    protected function casts(): array
    {
        return [
            'sensor_oids' => 'array',
        ];
    }

    // ---- Helper Functions ----

    public function classDescr()
    {
        return __('wireless.' . $this->sensor_class . '.short');
    }

    public function icon(): string
    {
        return collect(collect(\ObzoraNMS\Device\WirelessSensor::getTypes())
            ->get($this->sensor_class, []))
            ->get('icon', 'signal');
    }

    public function unit(): string
    {
        return __('wireless.' . $this->sensor_class . '.unit');
    }

    public function getGraphType(): string
    {
        return 'wireless_' . $this->sensor_class;
    }

    public function formatValue($field = 'sensor_current'): string
    {
        $value = $this->$field;

        if ($value === null) {
            return $field == 'sensor_current' ? 'NaN' : '-';
        }

        if (in_array($this->rrd_type, ['COUNTER', 'DERIVE', 'DCOUNTER', 'DDERIVE'])) {
            //compute and display an approx rate for this sensor
            $value = Number::formatSi(max(0, $value - $this->sensor_prev) / ObzoraConfig::get('rrd.step', 300), 2, 3, '');
        }

        return match ($this->sensor_class) {
            'power', 'rate' => Number::formatSi($value, 3, 0, $this->unit()),
            'frequency' => Number::formatSi($value * 1000000, 3, 0, 'Hz'),
            'distance' => Number::formatSi($value * 1000, 2, 3, 'm'),
            'dbm' => round($value, 3) . ' ' . $this->unit(),
            default => $value . ' ' . $this->unit(),
        };
    }

    public function getCompositeKey(): string
    {
        return "$this->sensor_class-$this->sensor_type-$this->sensor_index";
    }

    public function fillValue(array $values): self
    {
        if (empty($values)) {
            $this->sensor_current = null;

            return $this;
        }

        if (count($values) > 1) {
            // aggregate data
            if ($this->sensor_aggregator == 'avg') {
                $sensor_value = array_sum($values) / count($values);
            } else {
                // sum
                $sensor_value = array_sum($values);
            }
        } else {
            $sensor_value = Arr::first($values);
        }

        $sensor_value = Number::extract($sensor_value);

        if ($this->sensor_divisor) {
            $sensor_value = $sensor_value / $this->sensor_divisor;
        }

        $this->sensor_current = $sensor_value * $this->sensor_multiplier;

        return $this;
    }
}
