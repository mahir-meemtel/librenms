<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Gotify extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        $url = "{$this->config['gotify-server-url']}/message";
        $token = "{$this->config['gotify-token']}";

        /**
         * Maps severity levels in ObzoraNMS to Gotify values.
         *
         * 0-3 Notifiction  = OK
         * 4-7 + Sound      = Warning
         * 8-10 + Vibration = Critical
         */
        switch ($alert_data['severity']) {
            case 'critical':
                $priority = 8;
                break;
            case 'warning':
                $priority = 4;
                break;
            default:
                $priority = 0;
        }

        $data = [
            'title' => $alert_data['title'],
            'message' => preg_replace('/([a-z0-9]+)_([a-z0-9]+)/', "$1\_$2", $alert_data['msg']),
            'priority' => $priority,
            'extras' => [
                'client::display' => [
                    'contentType' => 'text/markdown',
                ],
                'monitoring::obzora' => [
                    'device_id' => $alert_data['device_id'],
                    'hostname' => $alert_data['hostname'],
                    'sysName' => $alert_data['sysName'],
                    'sysDescr' => $alert_data['sysDescr'],
                    'display' => $alert_data['display'],
                    'sysContact' => $alert_data['sysContact'],
                    'os' => $alert_data['os'],
                    'type' => $alert_data['type'],
                    'ip' => $alert_data['ip'],
                    'version' => $alert_data['version'],
                    'hardware' => $alert_data['hardware'],
                    'features' => $alert_data['features'],
                    'serial' => $alert_data['serial'],
                    'location' => $alert_data['location'],
                    'uptime' => $alert_data['uptime'],
                    'uptime_short' => $alert_data['uptime_short'],
                    'uptime_long' => $alert_data['uptime_long'],
                    'description' => $alert_data['description'],
                    'notes' => $alert_data['notes'],
                    'alert_notes' => $alert_data['alert_notes'],
                    'id' => $alert_data['id'],
                    'uid' => $alert_data['uid'],
                    'state' => $alert_data['state'],
                    'severity' => $alert_data['severity'],
                    'rule' => $alert_data['rule'],
                    'name' => $alert_data['name'],
                    'proc' => $alert_data['proc'],
                    'timestamp' => $alert_data['timestamp'],
                ],
            ],
        ];

        $res = Http::client()
            ->withHeaders(
                [
                    'X-Gotify-Key' => $token,
                    'Content-Type' => 'application/json',
                ]
            )
            ->acceptJson()
            ->post($url, $data);

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
                    'title' => 'Server (URL)',
                    'name' => 'gotify-server-url',
                    'descr' => 'Gotify Server Address',
                    'type' => 'text',
                ],
                [
                    'title' => 'Token',
                    'name' => 'gotify-token',
                    'descr' => 'Gotify Token',
                    'type' => 'password',
                ],
            ],
            'validation' => [
                'gotify-server-url' => 'required|string',
                'gotify-token' => 'required|string',
            ],
        ];
    }
}
