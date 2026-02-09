<?php
namespace ObzoraNMS\Alert\Transport;

use App\Facades\DeviceCache;
use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;
use ObzoraNMS\Util\Url;

class Grafana extends Transport
{
    protected string $name = 'Grafana Oncall';

    public function deliverAlert(array $alert_data): bool
    {
        $device = DeviceCache::get($alert_data['device_id']);

        $graph_args = [
            'type' => 'device_bits', // FIXME use graph url related to alert
            'device' => $device['device_id'],
            'height' => 150,
            'width' => 300,
            'legend' => 'no',
            'title' => 'yes',
        ];

        //$graph_url = url('graph.php') . '?' . http_build_query($graph_args);
        // FIXME - workaround for https://github.com/grafana/oncall/issues/3031
        $graph_url = url('graph.php') . '/' . str_replace('&', '/', http_build_query($graph_args));

        $data = [
            'alert_uid' => $alert_data['id'],
            'title' => $alert_data['title'] ?? null,
            'image_url' => $graph_url,
            'link_to_upstream_details' => Url::deviceUrl($device),
            'state' => ($alert_data['state'] == AlertState::ACTIVE) ? 'alerting' : 'ok',
            'raw_state' => $alert_data['state'],
            'device_id' => $alert_data['device_id'],
            'hostname' => $alert_data['hostname'],
            'sysName' => $alert_data['sysName'],
            'location' => $alert_data['location'],
            'sysDescr' => $alert_data['sysDescr'],
            'os' => $alert_data['os'],
            'type' => $alert_data['type'],
            'hardware' => $alert_data['hardware'],
            'software' => $alert_data['software'] ?? '',
            'features' => $alert_data['features'],
            'serial' => $alert_data['serial'] ?? '',
            'uptime' => $alert_data['uptime'],
            'notes' => $alert_data['notes'],
            'alert_notes' => $alert_data['alert_notes'],
            'severity' => $alert_data['severity'],
            'proc' => $alert_data['proc'],
            'transport' => $alert_data['transport'] ?? '',
            'transport_name' => $alert_data['transport_name'] ?? '',
        ];

        $tmp_msg = json_decode($alert_data['msg'], true);
        if (isset($tmp_msg['title']) && isset($tmp_msg['message'])) {
            $data = array_merge($data, $tmp_msg);
        } else {
            $data['message'] = $alert_data['msg'];
        }

        $res = Http::client()->post($this->config['url'] ?? '', $data);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $alert_data['msg'], $data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Webhook URL',
                    'name' => 'url',
                    'descr' => 'Grafana Oncall Webhook URL',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'url' => 'required|url',
            ],
        ];
    }
}
