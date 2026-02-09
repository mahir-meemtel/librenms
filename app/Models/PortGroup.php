<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PortGroup extends BaseModel
{
    public $timestamps = false;
    protected $fillable = ['name', 'desc'];

    public function scopeHasAccess($query, User $user)
    {
        if ($user->hasGlobalRead()) {
            return $query;
        }

        // maybe filtered in future
        return $query;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Port, $this>
     */
    public function ports(): BelongsToMany
    {
        return $this->belongsToMany(Port::class, 'port_group_port', 'port_group_id', 'port_id');
    }
}
