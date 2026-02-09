<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Playsms extends Transport
{
    protected string $name = 'playSMS';

    public function deliverAlert(array $alert_data): bool
    {
        $to = preg_split('/([,\r\n]+)/', $this->config['playsms-mobiles']);

        $url = str_replace('?app=ws', '', $this->config['playsms-url']); // remove old format
        $data = [
            'app' => 'ws',
            'op' => 'pv',
            'u' => $this->config['playsms-user'],
            'h' => $this->config['playsms-token'],
            'to' => implode(',', $to),
            'msg' => $alert_data['title'],
        ];
        if (! empty($this->config['playsms-from'])) {
            $data['from'] = $this->config['playsms-from'];
        }

        $res = Http::client()->get($url, $data);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $data['msg'], $data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'PlaySMS URL',
                    'name' => 'playsms-url',
                    'descr' => 'PlaySMS URL',
                    'type' => 'text',
                ],
                [
                    'title' => 'User',
                    'name' => 'playsms-user',
                    'descr' => 'PlaySMS User',
                    'type' => 'text',
                ],
                [
                    'title' => 'Token',
                    'name' => 'playsms-token',
                    'descr' => 'PlaySMS Token',
                    'type' => 'password',
                ],
                [
                    'title' => 'From',
                    'name' => 'playsms-from',
                    'descr' => 'PlaySMS From',
                    'type' => 'text',
                ],
                [
                    'title' => 'Mobiles',
                    'name' => 'playsms-mobiles',
                    'descr' => 'PlaySMS Mobiles, can be new line or comma separated',
                    'type' => 'textarea',
                ],
            ],
            'validation' => [
                'playsms-url' => 'required|url',
                'playsms-user' => 'required|string',
                'playsms-token' => 'required|string',
                'playsms-mobiles' => 'required',
            ],
        ];
    }
}
