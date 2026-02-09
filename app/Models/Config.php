<?php
namespace App\Models;

class Config extends BaseModel
{
    public $timestamps = false;
    protected $table = 'config';
    public $primaryKey = 'config_id';
    protected $fillable = [
        'config_name',
        'config_value',
    ];

    /**
     * @return array{config_default: 'array'}
     */
    protected function casts(): array
    {
        return [
            'config_default' => 'array',
        ];
    }

    // ---- Accessors/Mutators ----

    public function getConfigValueAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setConfigValueAttribute($value)
    {
        $this->attributes['config_value'] = json_encode($value, JSON_UNESCAPED_SLASHES);
    }

    // ---- Query Scopes ----

    public function scopeWithChildren($query, $name)
    {
        return $query->where('config_name', $name)
            ->orWhere('config_name', 'like', "$name.%");
    }
}
