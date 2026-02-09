<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Opsgenie extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        $url = $this->config['genie-url'];

        $res = Http::client()->post($url, $alert_data);

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
                    'name' => 'genie-url',
                    'descr' => 'OpsGenie Webhook URL',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'genie-url' => 'required|url',
            ],
        ];
    }
}
