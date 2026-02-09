<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Messagebird extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        $messagebird_msg = mb_strimwidth($alert_data['msg'], 0, $this->config['messagebird-limit'] - 3, '...');
        $api_url = 'https://rest.messagebird.com/messages';
        $fields = [
            'recipients' => $this->config['messagebird-recipient'],
            'originator' => $this->config['messagebird-origin'],
            'body' => $messagebird_msg,
        ];

        $res = Http::client()
            ->withHeaders([
                'Authorization' => 'AccessKey ' . $this->config['messagebird-key'],
            ])
            ->post($api_url, $fields);

        if ($res->successful() && $res->status() == '201') {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $messagebird_msg, $fields);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Messagebird API key',
                    'name' => 'messagebird-key',
                    'descr' => 'Messagebird API REST key',
                    'type' => 'password',
                ],
                [
                    'title' => 'Messagebird originator',
                    'name' => 'messagebird-origin',
                    'descr' => 'Originator in E.164 format eg. +1555###****',
                    'type' => 'text',
                ],
                [
                    'title' => 'Messagebird recipients',
                    'name' => 'messagebird-recipient',
                    'descr' => 'Recipient in E.164 format eg. +1555###****',
                    'type' => 'text',
                ],
                [
                    'title' => 'Limit characters in text message',
                    'name' => 'messagebird-limit',
                    'descr' => 'Limit max characters',
                    'type' => 'text',
                    'default' => 120,
                ],
            ],
            'validation' => [
                'messagebird-key' => 'required',
                'messagebird-origin' => 'required',
                'messagebird-recipient' => 'required',
                'messagebird-limit' => 'integer|between:1,480',
            ],
        ];
    }
}
