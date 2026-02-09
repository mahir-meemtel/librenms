<?php
namespace ObzoraNMS\Interfaces\UI;

use App\Models\Device;
use Illuminate\Http\Request;

interface DeviceTab
{
    /**
     * Check if the tab is visible
     *
     * @param  Device  $device
     * @return bool
     */
    public function visible(Device $device): bool;

    /**
     * The url slug for this tab
     *
     * @return string
     */
    public function slug(): string;

    /**
     * The icon to display for this tab
     *
     * @return string
     */
    public function icon(): string;

    /**
     * Name to display for this tab
     *
     * @return string
     */
    public function name(): string;

    /**
     * Collect data to send to the view
     *
     * @param  Device  $device
     * @param  Request  $request
     * @return array
     */
    public function data(Device $device, Request $request): array;
}
