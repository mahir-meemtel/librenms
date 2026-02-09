<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ObzoraNMS\Util\StringHelpers;

class Application extends DeviceRelatedModel
{
    use SoftDeletes;
    public $timestamps = false;
    protected $primaryKey = 'app_id';
    protected $fillable = ['device_id', 'app_type', 'app_instance', 'app_status', 'app_state', 'data', 'deleted_at', 'discovered'];

    /**
     * @return array{data: 'array'}
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    // ---- Helper Functions ----

    public function displayName()
    {
        return StringHelpers::niceCase($this->app_type);
    }

    public function getShowNameAttribute()
    {
        return $this->displayName();
    }

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ApplicationMetric, $this>
     */
    public function metrics(): HasMany
    {
        return $this->hasMany(ApplicationMetric::class, 'app_id', 'app_id');
    }
}
