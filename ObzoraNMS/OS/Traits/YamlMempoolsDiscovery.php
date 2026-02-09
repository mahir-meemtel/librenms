<?php
namespace ObzoraNMS\OS\Traits;

use App\Models\Mempool;
use ObzoraNMS\Discovery\Yaml\IndexField;
use ObzoraNMS\Discovery\Yaml\LiteralField;
use ObzoraNMS\Discovery\Yaml\OidField;
use ObzoraNMS\Discovery\Yaml\YamlDiscoveryField;
use ObzoraNMS\Discovery\YamlDiscoveryDefinition;

trait YamlMempoolsDiscovery
{
    public function discoverYamlMempools(): \Illuminate\Support\Collection
    {
        $mempools_yaml = $this->getDiscovery('mempools');

        $def = YamlDiscoveryDefinition::make(Mempool::class)
            ->addField(new IndexField('index', 'mempool_index'))
            ->addField(new LiteralField('type', 'mempool_type', $this->getName()))
            ->addField(new LiteralField('class', 'mempool_class', 'system'))
            ->addField(new YamlDiscoveryField('precision', 'mempool_precision', 1))
            ->addField(new YamlDiscoveryField('descr', 'mempool_descr', 'Memory', callback: fn ($value) => ucwords($value)))
            ->addField(new OidField('used', 'mempool_used'))
            ->addField(new OidField('free', 'mempool_free', should_poll: function (YamlDiscoveryDefinition $def) {
                return ($def->getFieldCurrentValue('used') === null || $def->getFieldCurrentValue('total') === null) && is_numeric($def->getFieldCurrentValue('free'));
            }))
            ->addField(new OidField('total', 'mempool_total', should_poll: false))
            ->addField(new OidField('percent_used', 'mempool_perc'))
            ->addField(new YamlDiscoveryField('warn_percent', 'mempool_perc_warn', 90))
            ->afterEach(function (Mempool $mempool, YamlDiscoveryDefinition $def, $yaml, $index) {
                // fill missing values
                $mempool->fillUsage($mempool->mempool_used, $mempool->mempool_total, $mempool->mempool_free, $mempool->mempool_perc);
            });

        return $def->discover($mempools_yaml);
    }
}
