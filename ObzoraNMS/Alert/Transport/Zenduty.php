<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Zenduty extends Transport
{
    protected string $name = 'Zenduty';

    public function deliverAlert(array $alert_data): bool
    {
        $url = $this->config['zenduty-url'];
        // If the alert has recovered set to resolved
        if ($alert_data['state'] == 0) {
            $alert_type = 'resolved';
        } elseif ($alert_data['state'] == 2) {
            $alert_type = 'acknowledged';
        } else {
            $alert_type = $alert_data['severity'];
        }
        // Set the standard data ZD expects to see
        $msg = (json_decode($alert_data['msg'], true)) ? json_decode($alert_data['msg'], true) : $alert_data['msg'];
        $data = [
            'message' => $alert_data['title'],
            'alert_type' => $alert_type,
            'entity_id' => $alert_data['alert_id'],
            'payload' => [
                'hostname' => $alert_data['hostname'],
                'sysName' => $alert_data['sysName'],
                'id' => $alert_data['id'],
                'uid' => $alert_data['uid'],
                'sysDescr' => $alert_data['sysDescr'],
                'os' => $alert_data['os'],
                'type' => $alert_type,
                'ip' => $alert_data['ip'],
                'hardware' => $alert_data['hardware'],
                'version' => $alert_data['version'],
                'uptime' => $alert_data['uptime'],
                'uptime_short' => $alert_data['uptime_short'],
                'timestamp' => $alert_data['timestamp'],
                'description' => $alert_data['description'],
                'title' => $alert_data['title'],
                'msg' => $msg,
                'state' => $alert_data['state'],
            ],
            'urls' => [
                [
                    'link_url' => route('device', ['device' => $alert_data['device_id']]),
                    'link_text' => $alert_data['hostname'],
                ],
            ],
        ];

        if (isset($this->config['sla_id'])) {
            $data['sla'] = $this->config['sla_id'];
        }

        if (isset($this->config['escalation_policy_id'])) {
            $data['escalation_policy'] = $this->config['escalation_policy_id'];
        }

        $tmp_msg = json_decode($alert_data['msg'], true);
        if (isset($tmp_msg['message']) && isset($tmp_msg['summary'])) {
            $data = array_merge($data, $tmp_msg);
        } else {
            $data['summary'] = $alert_data['msg'];
        }

        $client = Http::client();

        $res = $client->withHeaders(
            [
                'Content-Type' => 'application/json',
            ]
        )->acceptJson()->post($url, $data);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $data['message'], $data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'ZenDuty WebHook',
                    'name' => 'zenduty-url',
                    'descr' => 'ZenDuty WebHook',
                    'type' => 'text',
                ],
                [
                    'title' => 'SLA ID',
                    'name' => 'sla_id',
                    'descr' => 'Unique ID of the SLA',
                    'type' => 'text',
                ],
                [
                    'title' => 'Escalation Policy ID',
                    'name' => 'escalation_policy_id',
                    'descr' => 'Unique ID of the Escalation Policy',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'zenduty-url' => 'required|url',
            ],
        ];
    }
}
