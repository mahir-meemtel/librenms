<?php
namespace App\Plugins\ExamplePlugin;

use App\Plugins\Hooks\PageHook;

// this page will be shown when the user clicks on the plugin from the plugins menu.
// This allows you to output a full screen of whatever you want to the user
class Page extends PageHook
{
    // point to the view for your plugin's settings
    // this is the default name so you can create the blade file as in this plugin
    // by omitting the variable, or point to another one

//    public string $view = 'resources.views.page';

    // The authorize method will determine if the user has access to this page.
    // if you want all users to be able to access this page simple return true
    public function authorize(\Illuminate\Contracts\Auth\Authenticatable $user): bool
    {
        // you can check user's roles like this:
//        return $user->can('admin');

        // or use whatever you like
//        return \Carbon\Carbon::now()->dayOfWeek == Carbon::THURSDAY; // only allowed access on Thursdays!

        return true; // allow every logged in user to access
    }

    // override the data function to add additional data to be accessed in the view
    // default just passes the stored data through
    // inside the blade, all variables will be named based on the key in the returned array
    public function data(): array
    {
        // run any calculations here
        $username = auth()->user()->username;

        return [
            'something' => 'this is a variable and can be accessed with {{ $something }}',
            'hello' => 'Hello: ' . $username,
        ];
    }
}
