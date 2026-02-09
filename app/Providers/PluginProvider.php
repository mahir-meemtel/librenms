<?php
namespace App\Providers;

use App\Exceptions\PluginDoesNotImplementHookException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ObzoraNMS\Interfaces\Plugins\PluginManagerInterface;

class PluginProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PluginManagerInterface::class, function ($app) {
            return new \App\Plugins\PluginManager;
        });
    }

    public function boot(): void
    {
        $this->loadLocalPlugins($this->app->make(PluginManagerInterface::class));
    }

    /**
     * Load any local plugins these plugins must implement only one hook.
     */
    protected function loadLocalPlugins(PluginManagerInterface $manager): void
    {
        $plugin_view_location_registered = [];

        foreach (glob(base_path('app/Plugins/*/*.php')) as $file) {
            if (preg_match('#^(.*/([^/]+))/([^/.]+)\.php#', $file, $matches)) {
                $plugin_name = $matches[2]; // containing directory name
                if ($plugin_name == 'Hooks') {
                    continue;  // don't load the hooks :D
                }

                $class = $this->className($plugin_name, $matches[3]);
                $hook_type = $this->hookType($class);

                // publish hooks in class
                $hook_published = $manager->publishHook($plugin_name, $hook_type, $class);

                // register view namespace
                if ($hook_published && ! in_array($plugin_name, $plugin_view_location_registered)) {
                    $plugin_view_location_registered[] = $plugin_name;  // don't register twice
                    $this->loadViewsFrom($matches[1], $plugin_name);
                }
            }
        }
    }

    /**
     * Check if a hook is extended by the given class.
     *
     * @param  string  $class
     * @return string
     *
     * @throws PluginDoesNotImplementHookException
     */
    protected function hookType(string $class): string
    {
        foreach (class_implements($class) as $parent) {
            if (Str::startsWith($parent, 'ObzoraNMS\Interfaces\Plugins\Hooks\\')) {
                return $parent;
            }
        }

        throw new PluginDoesNotImplementHookException($class);
    }

    protected function className(string $dir, string $name): string
    {
        return 'App\Plugins\\' . $dir . '\\' . $name;
    }
}
