<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertTemplateMap extends BaseModel
{
    protected $table = 'alert_template_map';
    public $timestamps = false;

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\AlertTemplate, $this>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(AlertTemplate::class, 'alert_templates_id');
    }
}
