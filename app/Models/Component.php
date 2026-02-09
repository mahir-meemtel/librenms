<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Component extends DeviceRelatedModel
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'component';
    protected $fillable = ['device_id', 'type', 'label', 'status', 'disabled', 'ignore', 'error'];

    // ---- Accessors/Mutators ----

    public function setStatusAttribute($status)
    {
        $this->attributes['status'] = (int) $status;
    }

    public function setDisabledAttribute($disabled)
    {
        $this->attributes['disabled'] = (int) $disabled;
    }

    public function setIgnoreAttribute($ignore)
    {
        $this->attributes['ignore'] = (int) $ignore;
    }

    public function error(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => is_null($value) ? null : substr($value, 0, 255),
        );
    }

    public function label(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => is_null($value) ? null : substr($value, 0, 255),
        );
    }

    public function type(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => is_null($value) ? null : substr($value, 0, 50),
        );
    }

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ComponentStatusLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ComponentStatusLog::class, 'component_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ComponentPref, $this>
     */
    public function prefs(): HasMany
    {
        return $this->hasMany(ComponentPref::class, 'component', 'id');
    }
}
