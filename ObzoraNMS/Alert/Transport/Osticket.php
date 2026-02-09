<?php
namespace ObzoraNMS\Alert\Transport;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Osticket extends Transport
{
    protected string $name = 'osTicket';

    public function deliverAlert(array $alert_data): bool
    {
        $url = $this->config['os-url'];
        $token = $this->config['os-token'];
        $email = '';

        foreach (\ObzoraNMS\Util\Mail::parseEmails(ObzoraConfig::get('email_from')) as $from => $from_name) {
            $email = $from_name . ' <' . $from . '>';
            break;
        }

        $protocol = [
            'name' => 'ObzoraNMS',
            'email' => $email,
            'subject' => ($alert_data['name'] ? $alert_data['name'] . ' on ' . $alert_data['hostname'] : $alert_data['title']),
            'message' => strip_tags($alert_data['msg']),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'attachments' => [],
        ];

        $res = Http::client()->withHeaders([
            'X-API-Key' => $token,
        ])->post($url, $protocol);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $alert_data['msg'], $protocol);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'API URL',
                    'name' => 'os-url',
                    'descr' => 'osTicket API URL',
                    'type' => 'text',
                ],
                [
                    'title' => 'API Token',
                    'name' => 'os-token',
                    'descr' => 'osTicket API Token',
                    'type' => 'password',
                ],
            ],
            'validation' => [
                'os-url' => 'required|url',
                'os-token' => 'required|string',
            ],
        ];
    }
}
