<?php
namespace ObzoraNMS\OS;

use App\Models\Eventlog;
use App\Models\TnmsneInfo;
use App\Observers\ModuleModelObserver;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Enum\Severity;
use ObzoraNMS\Interfaces\Data\DataStorageInterface;
use ObzoraNMS\Interfaces\Polling\OSPolling;

class Coriant extends \ObzoraNMS\OS implements OSPolling
{
    public function pollOS(DataStorageInterface $datastore): void
    {
        Log::info('TNMS-NBI-MIB: enmsNETable');

        /*
         * Coriant have done some SQL over SNMP, since we have to populate and update all the tables
         * before using it, we have to do ugly stuff
         */

        $c_list = [];
        ModuleModelObserver::observe('\App\Models\MplsLsp\TnmsneInfo');

        foreach (snmpwalk_cache_multi_oid($this->getDeviceArray(), 'enmsNETable', [], 'TNMS-NBI-MIB') as $index => $ne) {
            $ne = TnmsneInfo::firstOrNew(['device_id' => $this->getDeviceId(), 'neID' => $index], [
                'device_id' => $this->getDeviceId(),
                'neID' => $index,
                'neType' => $ne['enmsNeType'],
                'neName' => $ne['enmsNeName'],
                'neLocation' => $ne['enmsNeLocation'],
                'neAlarm' => $ne['enmsNeAlarmSeverity'],
                'neOpMode' => $ne['enmsNeOperatingMode'],
                'neOpState' => $ne['enmsNeOpState'],
            ]);

            if ($ne->isDirty()) {
                $ne->save();
                Eventlog::log("Coriant enmsNETable Hardware $ne->neType : $ne->neName ($index) at $ne->neLocation Discovered", $this->getDevice(), 'system');
            }
            $c_list[] = $index;
        }

        foreach (TnmsneInfo::where('device_id', $this->getDeviceId())->whereNotIn('neID', $c_list)->get() as $ne) {
            /** @var TnmsneInfo $ne */
            $ne->delete();
            Eventlog::log("Coriant enmsNETable Hardware $ne->neName at $ne->neLocation Removed", $this->getDevice(), 'system', Severity::Info, $ne->neID);
        }
    }
}
