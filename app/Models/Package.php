<?php
namespace App\Models;

use ObzoraNMS\Interfaces\Models\Keyable;

class Package extends DeviceRelatedModel implements Keyable
{
    public $timestamps = false;
    protected $primaryKey = 'pkg_id';
    protected $fillable = [
        'name',
        'manager',
        'status',
        'version',
        'build',
        'arch',
        'size',
    ];

    public function getCompositeKey()
    {
        return "$this->manager-$this->name-$this->arch";
    }

    public function __toString()
    {
        return $this->name . ' (' . $this->arch . ') version ' . $this->version . ($this->build ? "-$this->build" : '');
    }

    public function isValid(): bool
    {
        return $this->name && $this->manager && $this->arch && $this->version;
    }
}
