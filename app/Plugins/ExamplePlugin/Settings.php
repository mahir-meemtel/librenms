<?php
namespace App\Plugins\ExamplePlugin;

use App\Plugins\Hooks\SettingsHook;

// In the plugins admin page, there will be a settings button if you implement this hook
// To save settings in your settings page, you should have a form that returns all variables
// you want to save in the database.
class Settings extends SettingsHook
{
    // point to the view for your plugin's settings
    // this is the default name so you can create the blade file as in this plugin
    // by ommitting the variable, or point to another one

//    public string $view = 'resources.views.settings';

    // override the data function to add additional data to be accessed in the view
    // default just passes the stored data through
    // inside the blade, all variables will be named based on the key in the returned array
    public function data(array $settings = []): array
    {
        // run any calculations here
        $total = array_sum([1, 2, 3, 4]);

        return [
            'settings' => $settings, // this is an array of all the settings stored in the database
            'something' => 'this is a variable and can be accessed with {{ $something }}',
            'total' => $total,
        ];
    }
}
