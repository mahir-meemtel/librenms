<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;
use ObzoraNMS\Util\Url;

class Alertmanager extends Transport
{
    protected string $name = 'Alert Manager';

    public function deliverAlert(array $alert_data): bool
    {
        $url = $this->config['alertmanager-url'];
        $username = $this->config['alertmanager-username'];
        $password = $this->config['alertmanager-password'];

        $alertmanager_status = $alert_data['state'] == AlertState::RECOVERED ? 'endsAt' : 'startsAt';
        $alertmanager_msg = strip_tags($alert_data['msg']);
        $data = [[
            $alertmanager_status => date('c'),
            'generatorURL' => Url::deviceUrl($alert_data['device_id']),
            'annotations' => [
                'summary' => $alert_data['name'],
                'title' => $alert_data['title'],
                'description' => $alertmanager_msg,
            ],
            'labels' => [
                'alertname' => $alert_data['name'],
                'severity' => $alert_data['severity'],
                'instance' => $alert_data['hostname'],
            ],
        ]];

        $alertmanager_opts = $this->parseUserOptions($this->config['alertmanager-options']);
        foreach ($alertmanager_opts as $label => $value) {
            // To allow dynamic values
            if (preg_match('/^extra_[A-Za-z0-9_]+$/', $label) && ! empty($alert_data['faults'][1][$value])) {
                $data[0]['labels'][$label] = strip_tags($alert_data['faults'][1][$value]);
            } else {
                $data[0]['labels'][$label] = strip_tags($value);
            }
        }

        $client = Http::client()->timeout(5);

        if ($username != '' && $password != '') {
            $client->withBasicAuth($username, $password);
        }

        foreach (explode(',', $url) as $am) {
            $post_url = ($am . '/api/v2/alerts');
            $res = $client->post($post_url, $data);

            if ($res->successful()) {
                return true;
            }
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $alertmanager_msg, $data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Alertmanager URL(s)',
                    'name' => 'alertmanager-url',
                    'descr' => 'Alertmanager Webhook URL(s). Can contain comma-separated URLs',
                    'type' => 'text',
                ],
                [
                    'title' => 'Alertmanager Username',
                    'name' => 'alertmanager-username',
                    'descr' => 'Alertmanager Basic Username to authenticate to Alertmanager',
                    'type' => 'text',
                ],
                [
                    'title' => 'Alertmanager Password',
                    'name' => 'alertmanager-password',
                    'descr' => 'Alertmanager Basic Password to authenticate to Alertmanager',
                    'type' => 'password',
                ],
                [
                    'title' => 'Alertmanager Options',
                    'name' => 'alertmanager-options',
                    'descr' => 'Alertmanager Options. You can add any fixed string value or dynamic value from alert details (label name must start with extra_ and value must exists in alert details).',
                    'type' => 'textarea',
                ],
            ],
            'validation' => [
                'alertmanager-url' => 'required|string',
            ],
        ];
    }
}
