<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollerGroup extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = ['group_name', 'descr'];

    /**
     * Initialize this class
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (PollerGroup $pollergroup) {
            // handle device poller group fallback to default poller
            $default_poller_id = \App\Facades\ObzoraConfig::get('default_poller_group');
            $pollergroup->devices()->update(['poller_group' => $default_poller_id]);
        });
    }

    public static function list()
    {
        return self::query()->pluck('group_name', 'id')->prepend(__('General'), 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Device, $this>
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'poller_group', 'id');
    }
}
