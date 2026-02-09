<?php
namespace ObzoraNMS\Modules;

use App\Facades\PortCache;
use App\Models\Device;
use App\Models\Ipv4Address;
use App\Models\Ipv4Network;
use App\Observers\ModuleModelObserver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\DB\SyncsModels;
use ObzoraNMS\Exceptions\InvalidIpException;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Discovery\Ipv4AddressDiscovery;
use ObzoraNMS\Interfaces\Module;
use ObzoraNMS\OS;
use ObzoraNMS\Polling\ModuleStatus;
use ObzoraNMS\Util\IPv4;
use SnmpQuery;

class Ipv4Addresses implements Module
{
    use SyncsModels;

    /**
     * @inheritDoc
     */
    public function dependencies(): array
    {
        return ['ports'];
    }

    /**
     * @inheritDoc
     */
    public function shouldDiscover(OS $os, ModuleStatus $status): bool
    {
        return $status->isEnabledAndDeviceUp($os->getDevice());
    }

    /**
     * @inheritDoc
     */
    public function shouldPoll(OS $os, ModuleStatus $status): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function discover(OS $os): void
    {
        $ips = new Collection;
        if ($os instanceof Ipv4AddressDiscovery) {
            $ips = $os->discoverIpv4Addresses();
        }
        if ($ips->isEmpty()) {
            $ips = $this->discoverIpMib($os->getDevice());
        }

        $ips = $ips->filter(function ($data) {
            $addr = trim(str_replace('"', '', $data->ipv4_address ?? ''));
            $context = trim(str_replace('"', '', $data->context_name ?? ''));
            $prefix = trim($data->ipv4_prefixlen ?? '');

            if ($prefix == 0 || $prefix == '0.0.0.0' || $prefix == '') {
                $prefix = IPv4::classfullNetmaskFromRfc($addr);
                Log::info('Classfull netmask from RFC: ' . $addr . ' - ' . $prefix);
            }

            if (empty($addr) || $addr == '0.0.0.0' || $prefix == '') { // invalid address or prefix
                Log::info('Invalid data: ' . $addr . ' / ' . $prefix);

                return null;
            }

            try {
                preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$/', (string) $prefix, $tmp); // is it netmask or cidr?
                $pfxLen = (empty($tmp[1])) ? intval($prefix) : IPv4::netmask2cidr($tmp[1]);
                Log::debug($addr . ' - ' . $pfxLen);
                $tst = new IPv4($addr . '/' . $pfxLen);
            } catch (InvalidIpException $e) {
                Log::error('Failed to parse IP: ' . $e->getMessage());

                return null;
            }

            if (! $data->port_id) {
                Log::debug('Skipping ' . $data->ipv4_address . ' due to no matching port');

                return null;
            }

            $data->ipv4_prefixlen = $pfxLen;
            $data->context_name = $context;

            return $data;
        });

        //create IPv4 Network
        $ips->each(function (Ipv4Address $ip) {
            if ($ip->ipv4_network_id === null && $ip->ipv4_prefixlen > 0 && $ip->ipv4_prefixlen < 32) {
                $addr = new IPv4($ip->ipv4_address . '/' . $ip->ipv4_prefixlen);
                $network = Ipv4Network::firstOrCreate([
                    'ipv4_network' => $addr->getNetwork(),
                    'context_name' => $ip->context_name,
                ]);

                $ip->ipv4_network_id = $network->ipv4_network_id;
            }
        });

        ModuleModelObserver::observe(Ipv4Address::class);
        $this->syncModels($os->getDevice(), 'ipv4', $ips);
    }

    /**
     * @inheritDoc
     */
    public function poll(OS $os, DataStorageInterface $datastore): void
    {
        // no polling
    }

    /**
     * @inheritDoc
     */
    public function dataExists(Device $device): bool
    {
        return $device->ipv4()->exists();
    }

    /**
     * @inheritDoc
     */
    public function cleanup(Device $device): int
    {
        return $device->ipv4()->delete();
    }

    /**
     * @inheritDoc
     */
    public function dump(Device $device, string $type): ?array
    {
        if ($type == 'polling') {
            return null;
        }

        return [
            'ipv4_addresses' => $device->ipv4()
                ->leftJoin('ipv4_networks', 'ipv4_addresses.ipv4_network_id', 'ipv4_networks.ipv4_network_id')
                ->select(['ipv4_addresses.*', 'ipv4_network', 'ifIndex']) // already joined with ports
                ->orderBy('ipv4_address')->orderBy('ipv4_prefixlen')->orderBy('ifIndex')->orderBy('ipv4_addresses.context_name')
                ->get()->map->makeHidden(['ipv4_address_id', 'ipv4_network_id', 'port_id', 'laravel_through_key']),
        ];
    }

    private function discoverIpMib(Device $device): Collection
    {
        $ips = new Collection;
        foreach ($device->getVrfContexts() as $context_name) {
            $ips = $ips->merge(SnmpQuery::context($context_name)->hideMib()->enumStrings()->walk(
                ['IP-MIB::ipAdEntAddr', 'IP-MIB::ipAdEntIfIndex', 'IP-MIB::ipAdEntNetMask'])
            ->mapTable(function ($data, $ipAddr = '') use ($context_name, $device) {
                //on some devices, ipAddr is broken, so use ipAdEntAddr as primary
                $entAddr = $data['ipAdEntAddr'] ?? '';
                $addr = (preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$/', (string) $entAddr, $tmp)) ? $entAddr : $ipAddr;

                return new Ipv4Address([
                    'port_id' => PortCache::getIdFromIfIndex($data['ipAdEntIfIndex'] ?? 0, $device),
                    'ipv4_address' => $addr,
                    'ipv4_prefixlen' => $data['ipAdEntNetMask'] ?? '',
                    'context_name' => $context_name,
                ]);
            }));
        }

        return $ips->filter();
    }
}
