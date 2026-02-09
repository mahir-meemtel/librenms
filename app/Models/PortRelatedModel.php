<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class PortRelatedModel extends BaseModel
{
    // ---- Query scopes ----

    public function scopeHasAccess($query, User $user)
    {
        return $this->hasPortAccess($query, $user);
    }

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Port, $this>
     */
    public function port(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'port_id', 'port_id');
    }
}
