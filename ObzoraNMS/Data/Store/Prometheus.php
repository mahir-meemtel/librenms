<?php
namespace ObzoraNMS\Data\Store;

use App\Facades\ObzoraConfig;
use App\Polling\Measure\Measurement;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Str;
use ObzoraNMS\Util\Http;
use Log;

class Prometheus extends BaseDatastore
{
    private $client;
    private $base_uri;

    private $enabled;
    private $prefix;

    public function __construct()
    {
        parent::__construct();

        $url = ObzoraConfig::get('prometheus.url');
        $job = ObzoraConfig::get('prometheus.job', 'obzora');
        $this->base_uri = "$url/metrics/job/$job/instance/";

        $this->client = Http::client()->baseUrl($this->base_uri);

        $user = ObzoraConfig::get('prometheus.user', '');
        $passwd = ObzoraConfig::get('prometheus.password', '');
        if ($user && $passwd) {
            $this->client = $this->client->withBasicAuth($user, $passwd);
        }

        $this->prefix = ObzoraConfig::get('prometheus.prefix', '');
        if ($this->prefix) {
            $this->prefix = "$this->prefix" . '_';
        }

        $this->enabled = self::isEnabled();
    }

    public function getName(): string
    {
        return 'Prometheus';
    }

    public static function isEnabled(): bool
    {
        return ObzoraConfig::get('prometheus.enable', false);
    }

    /**
     * @inheritDoc
     */
    public function write(string $measurement, array $fields, array $tags = [], array $meta = []): void
    {
        $stat = Measurement::start('put');
        // skip if needed
        if (! $this->enabled) {
            return;
        }

        $vals = '';
        $promtags = '/measurement/' . $measurement;

        foreach ($fields as $k => $v) {
            if ($v !== null) {
                $vals .= $this->prefix . "$k $v\n";
            }
        }

        foreach ($tags as $t => $v) {
            if ($v !== null) {
                $promtags .= (Str::contains($v, '/') ? "/$t@base64/" . base64_encode($v) : "/$t/$v");
            }
        }

        $device = $this->getDevice($meta);
        $promurl = $device->hostname . $promtags;
        if (ObzoraConfig::get('prometheus.attach_sysname', false)) {
            $promurl .= '/sysName/' . $device->sysName;
        }
        $promurl = str_replace(' ', '-', $promurl); // Prometheus doesn't handle tags with spaces in url

        Log::debug("Prometheus put $promurl: ", [
            'measurement' => $measurement,
            'tags' => $tags,
            'fields' => $fields,
            'vals' => $vals,
        ]);

        try {
            $result = $this->client->withBody($vals, 'text/plain')->post($promurl);

            $this->recordStatistic($stat->end());

            if (! $result->successful()) {
                Log::error('Prometheus Error: ' . $result->body());
            }
        } catch (ConnectionException $e) {
            \Illuminate\Support\Facades\Log::error("%RFailed to connect to Prometheus server $this->base_uri, temporarily disabling.%n", ['color' => true]);
            $this->enabled = false;
        }
    }
}
