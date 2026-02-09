# ObzoraNMS Plugin Interfaces

Plugins for [ObzoraNMS](https://obzora.meemtel.com) https://github.com/obzora/obzora

Create a new Laravel Package as described:
https://laravel.com/docs/packages

Require this package

    composer require obzora/plugin-interfaces


Register your plugin with ObzoraNMS in your provider boot method and check to see if it is enabled:

```php
    public function boot(): void
    {
        $pluginName = 'example-plugin';
        $pluginManager = $this->app->make(\ObzoraNMS\Interfaces\Plugins\PluginManagerInterface::class);

        $pluginManager->publishHook($pluginName, \ObzoraNMS\Interfaces\Plugins\MenuEntryHook::class, MenuEntryHook::class);

        if (! $pluginManager->pluginEnabled($pluginName)) {
            return; // if plugin is disabled, don't boot
        }

        // Do regular Laravel Package actions here, such as register routes and views or publish files.
    }
```
