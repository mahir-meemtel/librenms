<?php
namespace App\Plugins\Hooks;

use App\Models\Device;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

abstract class DeviceOverviewHook implements \ObzoraNMS\Interfaces\Plugins\Hooks\DeviceOverviewHook
{
    public string $view = 'resources.views.device-overview';

    public function authorize(User $user, Device $device): bool
    {
        return true;
    }

    public function data(Device $device): array
    {
        return [
            'title' => __CLASS__,
            'device' => $device,
        ];
    }

    final public function handle(string $pluginName, array $settings, Device $device, Application $app): \Illuminate\Contracts\View\View
    {
        return view(Str::start($this->view, "$pluginName::"), $app->call([$this, 'data'], [
            'device' => $device,
            'settings' => $settings,
        ]));
    }
}
