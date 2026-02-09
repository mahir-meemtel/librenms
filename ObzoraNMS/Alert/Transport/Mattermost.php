<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Mattermost extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        $host = $this->config['mattermost-url'];
        $mattermost_msg = strip_tags($alert_data['msg']);
        $color = self::getColorForState($alert_data['state']);
        $data = [
            'attachments' => [
                0 => [
                    'fallback' => $mattermost_msg,
                    'color' => $color,
                    'title' => $alert_data['title'],
                    'text' => $alert_data['msg'],
                    'mrkdwn_in' => ['text', 'fallback'],
                    'author_name' => $this->config['mattermost-author_name'],
                ],
            ],
            'channel' => $this->config['mattermost-channel'],
            'username' => $this->config['mattermost-username'],
            'icon_url' => $this->config['mattermost-icon'],
        ];

        $res = Http::client()->acceptJson()->post($host, $data);

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
                    'title' => 'Webhook URL',
                    'name' => 'mattermost-url',
                    'descr' => 'Mattermost Webhook URL',
                    'type' => 'text',
                ],
                [
                    'title' => 'Channel',
                    'name' => 'mattermost-channel',
                    'descr' => 'Mattermost Channel',
                    'type' => 'text',
                ],
                [
                    'title' => 'Username',
                    'name' => 'mattermost-username',
                    'descr' => 'Mattermost Username',
                    'type' => 'text',
                ],
                [
                    'title' => 'Icon',
                    'name' => 'mattermost-icon',
                    'descr' => 'Icon URL',
                    'type' => 'text',
                ],
                [
                    'title' => 'Author_name',
                    'name' => 'mattermost-author_name',
                    'descr' => 'Optional name used to identify the author',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'mattermost-url' => 'required|url',
                'mattermost-icon' => 'url',
            ],
        ];
    }
}
