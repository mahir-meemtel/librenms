<?php
namespace ObzoraNMS\Discovery\Yaml;

use ObzoraNMS\Discovery\YamlDiscoveryDefinition;

class OidField extends YamlDiscoveryField
{
    public bool $isOid = true;

    public function __construct(string $key, ?string $model_column = null, ?string $default = null, ?\Closure $callback = null, \Closure|bool|null $should_poll = null)
    {
        parent::__construct($key, $model_column, $default, $callback);

        // should poll heuristic
        if (is_bool($should_poll)) {
            $this->should_poll = fn (YamlDiscoveryDefinition $def) => $should_poll;
        } elseif ($should_poll === null) {
            $this->should_poll = fn (YamlDiscoveryDefinition $def) => is_numeric($this->value);
        } else {
            $this->should_poll = $should_poll;
        }
    }
}
