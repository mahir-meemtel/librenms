<?php
namespace ObzoraNMS\Util;

use App\Models\Device;
use ObzoraNMS\Interfaces\Geocoder;
use Net_DNS2_Resolver;

class Dns implements Geocoder
{
    protected Net_DNS2_Resolver $resolver;

    public function __construct(Net_DNS2_Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public static function lookupIp(Device $device): ?string
    {
        if (IP::isValid($device->hostname)) {
            return $device->hostname;
        }

        try {
            if ($device->transport == 'udp6' || $device->transport == 'tcp6') {
                return dns_get_record($device['hostname'], DNS_AAAA)[0]['ipv6'] ?? null;
            }

            return dns_get_record($device['hostname'], DNS_A)[0]['ip'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param  string  $domain  Domain which has to be parsed
     * @param  string  $record  DNS Record which should be searched
     * @return array List of matching records
     */
    public function getRecord($domain, $record = 'A')
    {
        try {
            $ret = $this->resolver->query($domain, $record);

            return $ret->answer;
        } catch (\Net_DNS2_Exception $e) {
            d_echo('::query() failed: ' . $e->getMessage());

            return [];
        }
    }

    public function getCoordinates($hostname)
    {
        $r = $this->getRecord($hostname, 'LOC');

        foreach ($r as $record) {
            return [
                'lat' => $record->latitude ?? null,
                'lng' => $record->longitude ?? null,
            ];
        }

        return [];
    }
}
