<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Pseudowire extends PortRelatedModel
{
    public $timestamps = false;
    protected $primaryKey = 'pseudowire_id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Pseudowire, $this>
     */
    public function endpoints(): HasMany
    {
        return $this->hasMany(Pseudowire::class, 'cpwVcId', 'cpwVcId');
    }
}
