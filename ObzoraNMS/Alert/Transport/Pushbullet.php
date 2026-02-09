<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Pushbullet extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        // Note: At this point it might be useful to iterate through $obj['contacts'] and send each of them a note ?
        $url = 'https://api.pushbullet.com/v2/pushes';
        $data = ['type' => 'note', 'title' => $alert_data['title'], 'body' => $alert_data['msg']];

        $res = Http::client()
            ->withToken($this->config['pushbullet-token'])
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
                    'title' => 'Access Token',
                    'name' => 'pushbullet-token',
                    'descr' => 'Pushbullet Access Token',
                    'type' => 'password',
                ],
            ],
            'validation' => [
                'pushbullet-token' => 'required|string',
            ],
        ];
    }
}
