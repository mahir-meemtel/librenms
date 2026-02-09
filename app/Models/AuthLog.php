<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    public $timestamps = false;
    protected $table = 'authlog';

    /**
     * @return array{datetime: 'datetime'}
     */
    protected function casts(): array
    {
        return [
            'datetime' => 'datetime',
        ];
    }
}
