<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\Models\Keyable;

class StateTranslation extends Model implements Keyable
{
    const CREATED_AT = null;
    const UPDATED_AT = 'state_lastupdated';
    protected $primaryKey = 'state_translation_id';
    protected $fillable = [
        'state_descr',
        'state_draw_graph',
        'state_value',
        'state_generic_value',
    ];

    public function severity(): Severity
    {
        return Severity::tryFrom((int) $this->state_generic_value) ?? Severity::Unknown;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\StateIndex, $this>
     */
    public function stateIndex(): BelongsTo
    {
        return $this->belongsTo(StateIndex::class, 'state_index_id', 'state_index_id');
    }

    public function getCompositeKey()
    {
        return $this->state_value;
    }
}
