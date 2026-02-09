<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Smsfeedback extends Transport
{
    protected string $name = 'SMSfeedback';

    public function deliverAlert(array $alert_data): bool
    {
        $url = 'http://api.smsfeedback.ru/messages/v2/send/';
        $params = [
            'phone' => $this->config['smsfeedback-mobiles'],
            'text' => $alert_data['title'],
            'sender' => $this->config['smsfeedback-sender'],
        ];

        $res = Http::client()
            ->withBasicAuth($this->config['smsfeedback-user'], $this->config['smsfeedback-pass'])
            ->get($url, $params);

        if ($res->successful() && str_starts_with($res->body(), 'accepted')) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $alert_data['title'], $params);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'User',
                    'name' => 'smsfeedback-user',
                    'descr' => 'smsfeedback User',
                    'type' => 'text',
                ],
                [
                    'title' => 'Password',
                    'name' => 'smsfeedback-pass',
                    'descr' => 'smsfeedback Password',
                    'type' => 'password',
                ],
                [
                    'title' => 'Mobiles',
                    'name' => 'smsfeedback-mobiles',
                    'descr' => 'smsfeedback Mobile number',
                    'type' => 'textarea',
                ],
                [
                    'title' => 'Sender',
                    'name' => 'smsfeedback-sender',
                    'descr' => 'smsfeedback sender name',
                    'type' => 'textarea',
                ],
            ],
            'validation' => [
                'smsfeedback-user' => 'required|string',
                'smsfeedback-pass' => 'required|string',
                'smsfeedback-mobiles' => 'required',
                'smsfeedback-sender' => 'required|string',
            ],
        ];
    }
}
