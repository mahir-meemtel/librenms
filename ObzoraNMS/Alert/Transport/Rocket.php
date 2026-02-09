<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Rocket extends Transport
{
    protected string $name = 'Rocket Chat';

    public function deliverAlert(array $alert_data): bool
    {
        $rocket_opts = $this->parseUserOptions($this->config['rocket-options']);

        $rocket_msg = strip_tags($alert_data['msg']);
        $data = [
            'attachments' => [
                0 => [
                    'fallback' => $rocket_msg,
                    'color' => self::getColorForState($alert_data['state']),
                    'title' => $alert_data['title'],
                    'text' => $rocket_msg,
                ],
            ],
            'channel' => $rocket_opts['channel'] ?? null,
            'username' => $rocket_opts['username'] ?? null,
            'icon_url' => $rocket_opts['icon_url'] ?? null,
            'icon_emoji' => $rocket_opts['icon_emoji'] ?? null,
        ];

        $res = Http::client()->post($this->config['rocket-url'], $data);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $rocket_msg, $data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Webhook URL',
                    'name' => 'rocket-url',
                    'descr' => 'Rocket.chat Webhook URL',
                    'type' => 'text',
                ],
                [
                    'title' => 'Rocket.chat Options',
                    'name' => 'rocket-options',
                    'descr' => 'Rocket.chat Options',
                    'type' => 'textarea',
                ],
            ],
            'validation' => [
                'rocket-url' => 'required|url',
            ],
        ];
    }
}
