<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Plugin extends BaseModel
{
    public $timestamps = false;
    protected $primaryKey = 'plugin_id';
    protected $fillable = ['plugin_name', 'plugin_active', 'version', 'settings'];

    /**
     * @return array{plugin_active: 'bool', settings: 'array'}
     */
    protected function casts(): array
    {
        return [
            'plugin_active' => 'bool',
            'settings' => 'array',
        ];
    }

    // ---- Query scopes ----

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeIsActive($query)
    {
        return $query->where('plugin_active', 1);
    }

    public function scopeVersionOne($query)
    {
        return $query->where('version', 1);
    }

    public function scopeVersionTwo($query)
    {
        return $query->where('version', 2);
    }
}
