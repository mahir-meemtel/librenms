<?php
namespace ObzoraNMS\Discovery\Yaml;

use Illuminate\Support\Str;
use ObzoraNMS\Util\Oid;

class IndexField extends YamlDiscoveryField
{
    public function calculateValue(array $yaml, array $data, string $index, int $count): void
    {
        if (array_key_exists($this->key, $yaml)) {
            parent::calculateValue($yaml, $data, $index, $count);

            return;
        }

        if (Str::startsWith($index, '.') && Oid::of($index)->isNumeric()) {
            // if this is a full numeric oid instead of an index, assume it is a scalar
            $this->value = 0;

            return;
        }

        $this->value = $index;
    }
}
