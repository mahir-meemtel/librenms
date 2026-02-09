<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Facades\DeviceCache;
use App\Facades\ObzoraConfig;
use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class InventoryController implements DeviceTab
{
    private $type = null;

    public function __construct()
    {
        if (ObzoraConfig::get('enable_inventory')) {
            $device = DeviceCache::getPrimary();

            if ($device->entityPhysical()->exists()) {
                $this->type = 'entphysical';
            } elseif ($device->hostResources()->exists()) {
                $this->type = 'hrdevice';
            }
        }
    }

    public function visible(Device $device): bool
    {
        return $this->type !== null;
    }

    public function slug(): string
    {
        return 'inventory';
    }

    public function icon(): string
    {
        return 'fa-cube';
    }

    public function name(): string
    {
        return __('Inventory');
    }

    public function data(Device $device, Request $request): array
    {
        return [
            'tab' => $this->type, // inject to load correct legacy file
        ];
    }
}
