<?php
namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use ObzoraNMS\Data\Store\Datastore;
use ObzoraNMS\Interfaces\Data\Datastore as DatastoreContract;

class DatastoreServiceProvider extends ServiceProvider
{
    protected $namespace = 'ObzoraNMS\\Data\\Store\\';
    protected $stores = [
        'ObzoraNMS\Data\Store\Graphite',
        'ObzoraNMS\Data\Store\InfluxDB',
        'ObzoraNMS\Data\Store\InfluxDBv2',
        'ObzoraNMS\Data\Store\OpenTSDB',
        'ObzoraNMS\Data\Store\Prometheus',
        'ObzoraNMS\Data\Store\Rrd',
        'ObzoraNMS\Data\Store\Kafka',
    ];

    public function register(): void
    {
        // set up bindings
        foreach ($this->stores as $store) {
            $this->app->singleton($store);
        }

        // bind the Datastore object
        $this->app->singleton('Datastore', function (Application $app, $options) {
            // only tag datastores enabled by config
            $stores = array_filter($this->stores, function ($store) {
                /** @var DatastoreContract $store */
                return $store::isEnabled();
            });

            $app->tag($stores, ['datastore']);

            return new Datastore(iterator_to_array($app->tagged('datastore')));
        });

        // additional bindings
        $this->registerInflux();
        $this->registerKafka();
    }

    public function registerInflux()
    {
        $this->app->singleton('InfluxDB\Database', function ($app) {
            return \ObzoraNMS\Data\Store\InfluxDB::createFromConfig();
        });
    }

    public function registerKafka()
    {
        $this->app->singleton('RdKafka\Producer', function ($app) {
            return \ObzoraNMS\Data\Store\Kafka::getClient();
        });
    }
}
