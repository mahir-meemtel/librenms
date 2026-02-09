<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Pagerduty extends Transport
{
    protected string $name = 'PagerDuty';

    public function deliverAlert(array $alert_data): bool
    {
        $event_action = match ($alert_data['state']) {
            AlertState::RECOVERED => 'resolve',
            AlertState::ACKNOWLEDGED => 'acknowledge',
            default => 'trigger'
        };

        $safe_message = strip_tags($alert_data['msg']) ?: 'Test';
        $message = array_filter(explode("\n", $safe_message), function ($value): bool {
            return strlen($value) > 0;
        });
        $data = [
            'routing_key' => $this->config['service_key'],
            'event_action' => $event_action,
            'dedup_key' => (string) $alert_data['alert_id'],
            'payload' => [
                'custom_details' => ['message' => $message],
                'group' => (string) \DeviceCache::get($alert_data['device_id'])->groups->pluck('name'),
                'source' => $alert_data['hostname'],
                'severity' => $alert_data['severity'],
                'summary' => ($alert_data['name'] ? $alert_data['name'] . ' on ' . $alert_data['hostname'] : $alert_data['title']),
            ],
        ];

        // EU service region
        $url = match ($this->config['region']) {
            'EU' => 'https://events.eu.pagerduty.com/v2/enqueue',
            'US' => 'https://events.pagerduty.com/v2/enqueue',
            default => $this->config['custom-url'],
        };

        $res = Http::client()->post($url, $data);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), implode(PHP_EOL, $message), $data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Service Region',
                    'name' => 'region',
                    'descr' => 'Service Region of the PagerDuty account',
                    'type' => 'select',
                    'options' => [
                        'EU' => 'EU',
                        'US' => 'US',
                        'Custom URL' => 'CUSTOM',
                    ],
                ],
                [
                    'title' => 'Routing Key',
                    'type' => 'text',
                    'name' => 'service_key',
                ],
                [
                    'title' => 'Custom API URL',
                    'type' => 'text',
                    'name' => 'custom-url',
                    'descr' => 'Custom PagerDuty API URL',
                ],
            ],
            'validation' => [
                'region' => 'in:EU,US,CUSTOM',
                'custom-url' => 'url',
            ],
        ];
    }
}
