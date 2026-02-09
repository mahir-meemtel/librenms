<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Ilert extends Transport
{
    public function name(): string
    {
        return 'ilert';
    }

    public function deliverAlert(array $alert_data): bool
    {
        if ($alert_data['state'] == AlertState::RECOVERED) {
            $event_type = 'RESOLVE';
        } elseif ($alert_data['state'] == AlertState::ACKNOWLEDGED) {
            $event_type = 'ACCEPT';
        } else {
            $event_type = 'ALERT';
        }

        $data = [
            'integrationKey' => $this->config['integration-key'],
            'eventType' => $event_type,
            'alertKey' => (string) $alert_data['alert_id'],
            'summary' => $alert_data['title'],
            'details' => $alert_data['msg'],
            'priority' => ($alert_data['severity'] == 'Critical') ? 'HIGH' : 'LOW',
        ];

        $tmp_msg = json_decode($alert_data['msg'], true);
        if (isset($tmp_msg['summary']) && isset($tmp_msg['details'])) {
            $data = array_merge($data, $tmp_msg);
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Content-Length' => strlen(json_encode($data)),
        ];

        $res = Http::client()
            ->withHeaders($headers)
            ->post('https://api.ilert.com/api/events', $data);

        if ($res->successful() && $res->status() == '202') {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $alert_data['msg'], $data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Integration Key',
                    'name' => 'integration-key',
                    'descr' => 'ilert Integration Key',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'integration-key' => 'required|string',
            ],
        ];
    }
}
