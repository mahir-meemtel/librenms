<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomMapNodeImage extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'custom_map_node_image_id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CustomMapNode, $this>
     */
    public function nodes(): HasMany
    {
        return $this->hasMany(CustomMapNode::class, 'node_image_id');
    }
}
