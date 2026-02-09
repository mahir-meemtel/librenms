<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Smseagle extends Transport
{
    protected string $name = 'SMSEagle';

    public function deliverAlert(array $alert_data): bool
    {
        $url = $this->config['smseagle-url'] . '/http_api/send_sms';
        if (! str_starts_with($url, 'http')) {
            $url = 'http://' . $url;
        }

        $params = [];

        // use token if available
        if (empty($this->config['smseagle-token'])) {
            $params['login'] = $this->config['smseagle-user'];
            $params['pass'] = $this->config['smseagle-pass'];
        } else {
            $params['access_token'] = $this->config['smseagle-token'];
        }

        $params['to'] = implode(',', preg_split('/([,\r\n]+)/', $this->config['smseagle-mobiles']));
        $params['message'] = $alert_data['title'];

        $res = Http::client()->get($url, $params);

        if ($res->successful() && str_starts_with($res->body(), 'OK')) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $params['message'], $params);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'SMSEagle Base URL',
                    'name' => 'smseagle-url',
                    'descr' => 'SMSEagle Host',
                    'type' => 'text',
                ],
                [
                    'title' => 'Access Token',
                    'name' => 'smseagle-token',
                    'descr' => 'SMSEagle Access Token',
                    'type' => 'password',
                ],
                [
                    'title' => 'User',
                    'name' => 'smseagle-user',
                    'descr' => 'SMSEagle User',
                    'type' => 'text',
                ],
                [
                    'title' => 'Password',
                    'name' => 'smseagle-pass',
                    'descr' => 'SMSEagle Password',
                    'type' => 'password',
                ],
                [
                    'title' => 'Mobiles',
                    'name' => 'smseagle-mobiles',
                    'descr' => 'SMSEagle Mobiles, can be new line or comma separated',
                    'type' => 'textarea',
                ],
            ],
            'validation' => [
                'smseagle-url' => 'required|url',
                'smseagle-token' => 'required_without:smseagle-user,smseagle-pass|string',
                'smseagle-user' => 'required_without:smseagle-token|string',
                'smseagle-pass' => 'required_without:smseagle-token|string',
                'smseagle-mobiles' => 'required',
            ],
        ];
    }
}
