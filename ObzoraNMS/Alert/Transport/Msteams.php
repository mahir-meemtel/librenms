<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Msteams extends Transport
{
    protected string $name = 'Microsoft Teams';

    public function deliverAlert(array $alert_data): bool
    {
        $data = [
            'title' => $alert_data['title'],
            'themeColor' => self::getColorForState($alert_data['state']),
            'text' => strip_tags($alert_data['msg'], '<strong><em><h1><h2><h3><strike><ul><ol><li><pre><blockquote><a><img><p>'),
            'summary' => $alert_data['title'],
        ];

        $client = Http::client();

        // template will contain raw json
        if ($this->config['use-json'] === 'on') {
            $msg = $alert_data['uid'] === '000'
                ? $this->testJsonMessage() // use pre-made JSON for tests
                : $alert_data['msg'];

            $client->withBody($msg, 'application/json');
        }

        $res = $client->post($this->config['msteam-url'], $data);

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
                    'name' => 'msteam-url',
                    'descr' => 'Microsoft Teams Webhook URL',
                    'type' => 'text',
                ],
                [
                    'title' => 'Use JSON?',
                    'name' => 'use-json',
                    'descr' => 'Compose MessageCard with JSON rather than Markdown. Your template must be valid MessageCard JSON',
                    'type' => 'checkbox',
                    'default' => false,
                ],
            ],
            'validation' => [
                'msteam-url' => 'required|url',
            ],
        ];
    }

    private function testJsonMessage(): string
    {
        return '{
    "type": "message",
    "attachments": [
        {
            "contentType": "application/vnd.microsoft.card.adaptive",
            "contentUrl": null,
            "content": {
                "type": "AdaptiveCard",
                "body": [
                    {
                        "type": "TextBlock",
                        "size": "Medium",
                        "weight": "Bolder",
                        "text": "ObzoraNMS Test Adaptive Card"
                    },
                    {
                        "type": "TextBlock",
                        "text": "You have successfully sent a pre-formatted AdaptiveCard message to Teams.",
                        "wrap": true
                    },
                    {
                        "type": "TextBlock",
                        "text": "This does not test if your alert template is valid AdaptiveCard JSON.",
                        "isSubtle": true,
                        "wrap": true
                    }
                ],
                "$schema": "http://adaptivecards.io/schemas/adaptive-card.json",
                "version": "1.4"
            }
        }
    ]
}';
    }
}
