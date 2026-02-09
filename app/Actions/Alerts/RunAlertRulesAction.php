<?php
namespace App\Actions\Alerts;

use App\Models\Device;
use ObzoraNMS\Alert\AlertRules;

class RunAlertRulesAction
{
    /**
     * @var AlertRules
     */
    private $rules;
    /**
     * @var Device
     */
    private $device;

    public function __construct(Device $device, AlertRules $rules)
    {
        $this->rules = $rules;
        $this->device = $device;
    }

    public function execute(): void
    {
        // TODO inline logic
        include_once base_path('includes/common.php');
        include_once base_path('includes/dbFacile.php');
        $this->rules->runRules($this->device->device_id);
    }
}
