<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Signl4 extends Transport
{
    protected string $name = 'SIGNL4';

    public function deliverAlert(array $alert_data): bool
    {
        $url = $this->config['signl4-url'];

        $alert_status = match ($alert_data['state']) {
            AlertState::RECOVERED => 'resolved',
            AlertState::ACKNOWLEDGED => 'acknowledged',
            default => 'new'
        };

        $s4_data = [
            'X-S4-ExternalID' => (string) $alert_data['alert_id'],
            'X-S4-Status' => $alert_status,
            'Body' => $alert_data['alert_notes'],
        ];

        $data = array_merge($s4_data, $alert_data);

        $res = Http::client()->post($url, $data);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), '', $alert_data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Webhook URL',
                    'name' => 'signl4-url',
                    'descr' => 'SIGNL4 webhook URL including team or integration secret.',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'signl4-url' => 'required|url',
            ],
        ];
    }
}
