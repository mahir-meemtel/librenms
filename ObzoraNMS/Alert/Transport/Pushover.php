<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Pushover extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        $options = $this->parseUserOptions($this->config['options']);

        $url = 'https://api.pushover.net/1/messages.json';
        $data = [];
        $data['token'] = $this->config['appkey'];
        $data['user'] = $this->config['userkey'];
        // Entities are html encoded so this will cause them to be displayed correctly in pushover alerts
        $data['html'] = '1';
        switch ($alert_data['severity']) {
            case 'critical':
                $data['priority'] = 1;
                if (! empty($options['sound_critical'])) {
                    $data['sound'] = $options['sound_critical'];
                }
                break;
            case 'warning':
                $data['priority'] = 1;
                if (! empty($options['sound_warning'])) {
                    $data['sound'] = $options['sound_warning'];
                }
                break;
        }
        switch ($alert_data['state']) {
            case AlertState::RECOVERED:
                $data['priority'] = 0;
                if (! empty($options['sound_ok'])) {
                    $data['sound'] = $options['sound_ok'];
                }
                break;
        }
        $data['title'] = $alert_data['title'];
        $data['message'] = $alert_data['msg'];
        if ($options) {
            $data = array_merge($data, $options);
        }

        $res = Http::client()->asForm()->post($url, $data);

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
                    'title' => 'Api Key',
                    'name' => 'appkey',
                    'descr' => 'Api Key',
                    'type' => 'password',
                ],
                [
                    'title' => 'User Key',
                    'name' => 'userkey',
                    'descr' => 'User Key',
                    'type' => 'password',
                ],
                [
                    'title' => 'Pushover Options',
                    'name' => 'options',
                    'descr' => 'Pushover options',
                    'type' => 'textarea',
                ],
            ],
            'validation' => [
                'appkey' => 'required',
                'userkey' => 'required',
            ],
        ];
    }
}
