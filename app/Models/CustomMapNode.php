<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomMapNode extends BaseModel
{
    use HasFactory;
    protected $primaryKey = 'custom_map_node_id';

    public function linkedMapIsDown(): bool
    {
        return $this->linked_custom_map_id &&
            CustomMapNode::where('custom_map_id', $this->linked_custom_map_id)
                ->whereRelation('device', fn ($q) => $q->isDown())->exists();
    }

    public function scopeHasAccess($query, User $user)
    {
        if ($user->hasGlobalRead()) {
            return $query;
        }

        // Allow only if the user has access to the node
        return $this->hasDeviceAccess($query, $user);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CustomMap, $this>
     */
    public function map(): BelongsTo
    {
        return $this->belongsTo(CustomMap::class, 'custom_map_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Device, $this>
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CustomMap, $this>
     */
    public function linked_map(): BelongsTo
    {
        return $this->belongsTo(CustomMap::class, 'linked_custom_map_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CustomMapNodeImage, $this>
     */
    public function nodeimage(): BelongsTo
    {
        return $this->belongsTo(CustomMapNodeImage::class, 'node_image_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CustomMapEdge, $this>
     */
    public function edges1(): HasMany
    {
        return $this->hasMany(CustomMapEdge::class, 'custom_map_node_id1');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CustomMapEdge, $this>
     */
    public function edges2(): HasMany
    {
        return $this->hasMany(CustomMapEdge::class, 'custom_map_node_id2');
    }
}
