<?php
namespace App\Models;

class Mpls extends DeviceRelatedModel
{
    public $timestamps = false;
    protected $table = 'mpls_lsps';
    protected $primaryKey = 'lsp_id';
}
