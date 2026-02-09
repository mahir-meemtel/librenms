<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bill extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'bill_id';

    // ---- Query Scopes ----

    public function scopeHasAccess($query, User $user)
    {
        if ($user->hasGlobalRead()) {
            return $query;
        }

        return $query->join('bill_perms', 'bill_perms.bill_id', 'bills.bill_id')
            ->where('bill_perms.user_id', $user->user_id);
    }

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Port, $this>
     */
    public function ports(): BelongsToMany
    {
        return $this->belongsToMany(Port::class, 'bill_ports', 'bill_id', 'port_id');
    }
}
