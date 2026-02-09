<?php
namespace App\Plugins\Hooks;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

abstract class SettingsHook implements \ObzoraNMS\Interfaces\Plugins\Hooks\SettingsHook
{
    public string $view = 'resources.views.settings';

    public function authorize(User $user): bool
    {
        return true;
    }

    public function data(array $settings): array
    {
        return [
            'settings' => $settings,
        ];
    }

    final public function handle(string $pluginName, array $settings, Application $app): array
    {
        return array_merge([
            'content_view' => Str::start($this->view, "$pluginName::"),
        ], $this->data($app->call([$this, 'data'], [
            'settings' => $settings,
        ])));
    }
}
