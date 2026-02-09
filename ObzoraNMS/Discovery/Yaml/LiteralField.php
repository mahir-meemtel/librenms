<?php
namespace ObzoraNMS\Discovery\Yaml;

class LiteralField extends YamlDiscoveryField
{
    public function calculateValue(array $yaml, array $data, string $index, int $count): void
    {
        $this->value = $yaml[$this->key] ?? $this->default;
    }
}
