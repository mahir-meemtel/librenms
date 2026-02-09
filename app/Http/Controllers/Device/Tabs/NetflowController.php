<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class NetflowController implements DeviceTab
{
    public function visible(Device $device): bool
    {
        if (ObzoraConfig::get('nfsen_enable')) {
            foreach ((array) ObzoraConfig::get('nfsen_rrds', []) as $nfsenrrds) {
                if ($nfsenrrds[strlen($nfsenrrds) - 1] != '/') {
                    $nfsenrrds .= '/';
                }

                $nfsensuffix = ObzoraConfig::get('nfsen_suffix', '');

                if (ObzoraConfig::get('nfsen_split_char')) {
                    $basefilename_underscored = preg_replace('/\./', ObzoraConfig::get('nfsen_split_char'), $device->hostname);
                } else {
                    $basefilename_underscored = $device->hostname;
                }

                $nfsen_filename = preg_replace('/' . $nfsensuffix . '/', '', $basefilename_underscored);
                if (is_file($nfsenrrds . $nfsen_filename . '.rrd')) {
                    return true;
                }
            }
        }

        return false;
    }

    public function slug(): string
    {
        return 'netflow';
    }

    public function icon(): string
    {
        return 'fa-tint';
    }

    public function name(): string
    {
        return __('Netflow');
    }

    public function data(Device $device, Request $request): array
    {
        return [
            'tab' => 'nfsen',
        ];
    }
}
