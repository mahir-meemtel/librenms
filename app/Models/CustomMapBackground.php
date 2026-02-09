<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomMapBackground extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'custom_map_background_id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CustomMap, $this>
     */
    public function map(): BelongsTo
    {
        return $this->belongsTo(CustomMap::class, 'custom_map_id');
    }
}
