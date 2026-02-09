<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortsNac extends PortRelatedModel
{
    protected $table = 'ports_nac';
    protected $primaryKey = 'ports_nac_id';
    public $timestamps = true;
    protected $fillable = [
        'auth_id',
        'device_id',
        'port_id',
        'domain',
        'username',
        'mac_address',
        'ip_address',
        'vlan',
        'host_mode',
        'authz_status',
        'authz_by',
        'authc_status',
        'method',
        'timeout',
        'time_left',
        'time_elapsed',
    ];

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Device, $this>
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }
}
