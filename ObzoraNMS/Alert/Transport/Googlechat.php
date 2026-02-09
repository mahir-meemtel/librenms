<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Googlechat extends Transport
{
    protected string $name = 'Google Chat';

    public function deliverAlert(array $alert_data): bool
    {
        $data = ['text' => $alert_data['msg']];
        $res = Http::client()->post($this->config['googlechat-webhook'], $data);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $data['text'], $data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Webhook URL',
                    'name' => 'googlechat-webhook',
                    'descr' => 'Google Chat Room Webhook',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'googlechat-webhook' => 'required|string',
            ],
        ];
    }
}
