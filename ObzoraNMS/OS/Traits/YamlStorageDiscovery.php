<?php
namespace ObzoraNMS\OS\Traits;

use App\Models\Storage;
use Illuminate\Support\Collection;
use ObzoraNMS\Discovery\Yaml\IndexField;
use ObzoraNMS\Discovery\Yaml\LiteralField;
use ObzoraNMS\Discovery\Yaml\OidField;
use ObzoraNMS\Discovery\Yaml\YamlDiscoveryField;
use ObzoraNMS\Discovery\YamlDiscoveryDefinition;

trait YamlStorageDiscovery
{
    private array $storagePrefetch = [];

    public function discoverYamlStorage(): Collection
    {
        $discovery = YamlDiscoveryDefinition::make(Storage::class)
            ->addField(new LiteralField('poller_type', 'type', $this->getName()))
            ->addField(new YamlDiscoveryField('type', 'storage_type', 'Storage'))
            ->addField(new YamlDiscoveryField('descr', 'storage_descr', 'Disk {{ $index }}'))
            ->addField(new YamlDiscoveryField('units', 'storage_units', 1)) // 1 for percentage only storages
            ->addField(new OidField('size', 'storage_size', should_poll: false))
            ->addField(new OidField('used', 'storage_used'))
            ->addField(new OidField('free', 'storage_free', should_poll: function (YamlDiscoveryDefinition $def) {
                if ($def->getFieldCurrentValue('used') === null || $def->getFieldCurrentValue('size') === null) {
                    return is_numeric($def->getFieldCurrentValue('free'));
                }

                return false;
            }))
            ->addField(new OidField('percent_used', 'storage_perc'))
            ->addField(new YamlDiscoveryField('warn_percent', 'storage_perc_warn', \ObzoraConfig::get('storage_perc_warn', 80)))
            ->addField(new IndexField('index', 'storage_index', '{{ $index }}'))
            ->afterEach(function (Storage $storage, YamlDiscoveryDefinition $def, $yaml, $index) {
                // fill missing values
                $storage->fillUsage(
                    $storage->storage_used,
                    $storage->storage_size,
                    $storage->storage_free,
                    $storage->storage_perc,
                );
            });

        return $discovery->discover($this->getDiscovery('storage'));
    }
}
