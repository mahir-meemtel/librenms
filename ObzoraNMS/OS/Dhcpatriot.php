<?php
namespace ObzoraNMS\OS;

use App\Models\Device;
use DateTime;
use Illuminate\Support\Str;

class Dhcpatriot extends Shared\Unix
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml
        $license = snmp_get($this->getDeviceArray(), '.1.3.6.1.4.1.2021.51.12.4.1.2.7.76.73.67.69.78.83.69.1', '-Oqv');

        if (! empty($license)) {
            if ($license === 'FULL:0') {
                $device->features = 'Non-Expiry License';
            } elseif (Str::contains($license, 'LIMITED:')) {
                $ft_epoch = str_replace('LIMITED:', '', $license);
                $ft_dt = new DateTime("@$ft_epoch");
                $device->features = 'License Expires ' . $ft_dt->format('Y-m-d');
            }
        }
    }
}
