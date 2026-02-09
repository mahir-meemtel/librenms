<?php
namespace ObzoraNMS\Util;

use App\Facades\ObzoraConfig;
use App\Models\Device;
use ObzoraNMS\Modules\LegacyModule;
use ObzoraNMS\Polling\ModuleStatus;

class Module
{
    public static function exists(string $module_name): bool
    {
        if (class_exists(StringHelpers::toClass($module_name, '\\ObzoraNMS\\Modules\\'))) {
            return true;
        }

        return ObzoraConfig::has('discovery_modules.' . $module_name) || ObzoraConfig::has('poller_modules.' . $module_name);
    }

    public static function fromName(string $module_name): \ObzoraNMS\Interfaces\Module
    {
        $module_class = StringHelpers::toClass($module_name, '\\ObzoraNMS\\Modules\\');

        return class_exists($module_class) ? new $module_class : new LegacyModule($module_name);
    }

    public static function legacyDiscoveryExists(string $module_name): bool
    {
        return is_file(base_path("includes/discovery/$module_name.inc.php"));
    }

    public static function legacyPollingExists(string $module_name): bool
    {
        return is_file(base_path("includes/polling/$module_name.inc.php"));
    }

    public static function pollingStatus(string $module_name, Device $device, ?bool $manual = null): ModuleStatus
    {
        return new ModuleStatus(
            ObzoraConfig::get("poller_modules.$module_name"),
            ObzoraConfig::get("os.{$device->os}.poller_modules.$module_name"),
            $device->getAttrib("poll_$module_name"),
            $manual,
        );
    }

    public static function parseUserOverrides(array $overrides): array
    {
        $modules = [];

        foreach ($overrides as $index => $module) {
            // parse submodules (only supported by some modules)
            if (str_contains($module, '/')) {
                [$module, $submodule] = explode('/', $module, 2);
                $modules[$module][] = $submodule;
            } elseif (self::exists($module)) {
                $modules[$module] = true;
            }
        }

        return $modules;
    }
}
