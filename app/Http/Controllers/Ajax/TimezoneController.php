<?php
namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimezoneController extends Controller
{
    public function set(Request $request): string
    {
        $request->session()->put('preferences.timezone_static', $request->boolean('static'));

        // laravel session
        if ($request->timezone) {
            // Only accept valid timezones
            if (! in_array($request->timezone, timezone_identifiers_list())) {
                return session('preferences.timezone', '');
            }

            $request->session()->put('preferences.timezone', $request->timezone);

            return $request->timezone;
        }

        return session('preferences.timezone', '');
    }
}
