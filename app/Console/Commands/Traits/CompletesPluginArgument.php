<?php
namespace App\Console\Commands\Traits;

use App\Models\Plugin;

trait CompletesPluginArgument
{
    /**
     * @param  string  $name
     * @param  string  $value
     * @param  string  $previous
     * @return array|false
     */
    public function completeArgument($name, $value, $previous)
    {
        if ($name == 'plugin') {
            return Plugin::where('plugin_name', 'like', $value . '%')
                ->pluck('plugin_name')
                ->toArray();
        }

        return false;
    }
}
