<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Callback extends Model
{
    public $timestamps = false;
    protected $table = 'callback';
    protected $primaryKey = 'callback_id';
    protected $fillable = ['name', 'value'];

    public static function get($name)
    {
        return static::query()->where('name', $name)->value('value');
    }

    public static function set($name, $value)
    {
        return static::query()->updateOrCreate(['name' => $name], ['name' => $name, 'value' => $value]);
    }
}
