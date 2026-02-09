<?php
namespace App\Plugins\Hooks;

use App\Models\Port;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

abstract class PortTabHook implements \ObzoraNMS\Interfaces\Plugins\Hooks\PortTabHook
{
    /** @var string */
    public $view = 'resources.views.port-tab';

    public function authorize(User $user, Port $port): bool
    {
        return true;
    }

    public function data(Port $port): array
    {
        return [
            'title' => __CLASS__,
            'port' => $port,
        ];
    }

    final public function handle(string $pluginName, Port $port, array $settings, Application $app): \Illuminate\Contracts\View\View
    {
        return view(Str::start($this->view, "$pluginName::"), $app->call([$this, 'data'], [
            'port' => $port,
            'settings' => $settings,
        ]));
    }
}
