<?php
namespace ObzoraNMS\OS\Traits;

use App\Models\Vminfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use ObzoraNMS\Enum\PowerState;

trait VminfoVmware
{
    public function discoverVmInfo(): Collection
    {
        Log::info('VMware VM: ');

        /*
         * Fetch the Virtual Machine information.
         *
         *  VMWARE-VMINFO-MIB::vmwVmDisplayName.224 = STRING: My First VM
         *  VMWARE-VMINFO-MIB::vmwVmGuestOS.224 = STRING: windows7Server64Guest
         *  VMWARE-VMINFO-MIB::vmwVmMemSize.224 = INTEGER: 8192 megabytes
         *  VMWARE-VMINFO-MIB::vmwVmState.224 = STRING: poweredOn
         *  VMWARE-VMINFO-MIB::vmwVmVMID.224 = INTEGER: 224
         *  VMWARE-VMINFO-MIB::vmwVmCpus.224 = INTEGER: 2
         */

        $vm_info = \SnmpQuery::hideMib()->walk('VMWARE-VMINFO-MIB::vmwVmTable');

        return $vm_info->mapTable(function ($data, $vmwVmVMID) {
            $data['vm_type'] = 'vmware';
            $data['vmwVmVMID'] = $vmwVmVMID;
            $data['vmwVmState'] = PowerState::STATES[$data['vmwVmState']] ?? PowerState::UNKNOWN;

            /*
             * If VMware Tools is not running then don't overwrite the GuestOS with the error
             * message, but just leave it as it currently is.
             */
            if (str_contains($data['vmwVmGuestOS'], 'tools not ')) {
                unset($data['vmwVmGuestOS']);
            }

            return new Vminfo($data);
        });
    }
}
