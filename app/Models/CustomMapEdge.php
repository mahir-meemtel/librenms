<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomMapEdge extends BaseModel
{
    use HasFactory;
    protected $primaryKey = 'custom_map_edge_id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CustomMap, $this>
     */
    public function map(): BelongsTo
    {
        return $this->belongsTo(CustomMap::class, 'custom_map_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Port, $this>
     */
    public function port(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'port_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CustomMapEdge, $this>
     */
    public function edges(): HasMany
    {
        return $this->hasMany(CustomMapEdge::class, 'custom_map_edge_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CustomMapNode, $this>
     */
    public function node1(): BelongsTo
    {
        return $this->belongsTo(CustomMapNode::class, 'custom_map_node1_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CustomMapNode, $this>
     */
    public function node2(): BelongsTo
    {
        return $this->belongsTo(CustomMapNode::class, 'custom_map_node2_id');
    }
}
