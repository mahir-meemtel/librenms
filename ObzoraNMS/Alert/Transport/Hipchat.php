<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Hipchat extends Transport
{
    protected string $name = 'HipChat';

    public function deliverAlert(array $alert_data): bool
    {
        $options = $this->parseUserOptions($this->config['hipchat-options']);

        // override legacy options
        if (array_key_exists('hipchat-notify', $this->config)) {
            $options['notify'] = ($this->config['hipchat-notify'] == 'on');
        }
        if (isset($this->config['hipchat-message_format'])) {
            $options['message_format'] = $this->config['hipchat-message_format'];
        }

        $url = $this->config['hipchat-url'];
        $version = str_contains($url, 'v2') ? 2 : 1;

        // Generate our URL from the base URL + room_id and the auth token if the version is 2.
        if ($version == 2) {
            $url .= '/' . urlencode($this->config['hipchat-room-id']) . '/notification';
        }

        // Sane default of making the message color green if the message indicates
        // that the alert recovered.   If it rebooted, make it yellow.
        if ($alert_data['state'] == AlertState::RECOVERED) {
            $color = 'green';
        } elseif (str_contains($alert_data['msg'], 'rebooted')) {
            $color = 'yellow';
        } elseif (empty($options['color']) || $options['color'] == 'u') {
            $color = 'red';
        } else {
            $color = $options['color'];
        }

        $data = [
            'message' => $alert_data['msg'],
            'from' => $this->config['hipchat-from-name'] ?: 'ObzoraNMS',
            'color' => $color,
            'notify' => $options['notify'] ?? '1',
            'message_format' => $options['message_format'] ?: 'text',
        ];
        if ($version == 1) {
            $data['room_id'] = $this->config['hipchat-room-id'];
        }

        $client = Http::client();

        if ($version == 2) {
            $client->withToken($options['auth_token']);
        }

        $res = $client->post($url, $data);

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
                    'title' => 'API URL',
                    'name' => 'hipchat-url',
                    'descr' => 'Hipchat API URL',
                    'type' => 'text',
                ],
                [
                    'title' => 'Room ID',
                    'name' => 'hipchat-room-id',
                    'descr' => 'Hipchat Room ID',
                    'type' => 'text',
                ],
                [
                    'title' => 'From Name',
                    'name' => 'hipchat-from-name',
                    'descr' => 'From Name',
                    'type' => 'text',
                ],
                [
                    'title' => 'Hipchat Options',
                    'name' => 'hipchat-options',
                    'descr' => 'Hipchat Options',
                    'type' => 'textarea',
                ],
                [
                    'title' => 'Notify?',
                    'name' => 'hipchat-notify',
                    'descr' => 'Send notification',
                    'type' => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'title' => 'Message Format',
                    'name' => 'hipchat-message_format',
                    'descr' => 'Format of message',
                    'type' => 'select',
                    'options' => [
                        'Text' => 'text',
                        'HTML' => 'html',
                    ],
                    'default' => 'text',
                ],
            ],
            'validation' => [
                'hipchat-url' => 'required|url',
                'hipchat-room-id' => 'required|numeric',
            ],
        ];
    }
}
