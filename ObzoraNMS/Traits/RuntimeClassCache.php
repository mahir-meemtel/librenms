<?php
namespace ObzoraNMS\Traits;

use Illuminate\Support\Facades\Cache;

trait RuntimeClassCache
{
    /** @var array */
    private $runtimeCache = [];

    /** @var int Setting this installs the data in the external cache to be shared across instances */
    protected $runtimeCacheExternalTTL = 0;

    /**
     * We want these each runtime, so don't use the global cache
     *
     * @return mixed
     */
    protected function cacheGet(string $name, callable $actual)
    {
        if (! array_key_exists($name, $this->runtimeCache)) {
            if ($this->runtimeCacheExternalTTL) {
                try {
                    $this->runtimeCache[$name] = Cache::remember('runtimeCache' . __CLASS__ . $name, $this->runtimeCacheExternalTTL, $actual);

                    return $this->runtimeCache[$name]; // system cache success, don't use local cache
                } catch (\Exception $e) {
                    // go to fallback code
                }
            }

            // non-persistent cache
            $this->runtimeCache[$name] = $actual();
        }

        return $this->runtimeCache[$name];
    }
}
