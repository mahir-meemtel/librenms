<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Clickatell extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        $url = 'https://platform.clickatell.com/messages/http/send';
        $params = [
            'apiKey' => $this->config['clickatell-token'],
            'to' => implode(',', preg_split('/([,\r\n]+)/', $this->config['clickatell-numbers'])),
            'content' => $alert_data['title'],
        ];

        $res = Http::client()->get($url, $params);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $alert_data['title'], $params);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Token',
                    'name' => 'clickatell-token',
                    'descr' => 'Clickatell Token',
                    'type' => 'password',
                ],
                [
                    'title' => 'Mobile Numbers',
                    'name' => 'clickatell-numbers',
                    'descr' => 'Enter mobile numbers, can be new line or comma separated',
                    'type' => 'textarea',
                ],
            ],
            'validation' => [
                'clickatell-token' => 'required|string',
                'clickatell-numbers' => 'required|string',
            ],
        ];
    }
}
