<?php
namespace App\Plugins\Hooks;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

abstract class MenuEntryHook implements \ObzoraNMS\Interfaces\Plugins\Hooks\MenuEntryHook
{
    public string $view = 'resources.views.menu';

    public function authorize(User $user): bool
    {
        return true;
    }

    public function data(): array
    {
        return [];
    }

    final public function handle(string $pluginName, array $settings, Application $app): array
    {
        return [Str::start($this->view, "$pluginName::"), $app->call([$this, 'data'], [
            'settings' => $settings,
        ])];
    }
}
