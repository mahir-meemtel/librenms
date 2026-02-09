<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class AlertTemplate extends BaseModel
{
    public $timestamps = false;

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\AlertTemplateMap, $this>
     */
    public function map(): HasMany
    {
        return $this->hasMany(AlertTemplateMap::class, 'alert_templates_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\AlertRule, \App\Models\AlertTemplateMap, $this>
     */
    public function alert_rules(): HasManyThrough
    {
        return $this->hasManyThrough(AlertRule::class, AlertTemplateMap::class, 'alert_templates_id', 'id', 'id', 'alert_rule_id')
                    ->select(['id' => 'alert_rules.id', 'name' => 'alert_rules.name'])
                    ->orderBy('alert_rules.name');
    }
}
