<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Victorops extends Transport
{
    protected string $name = 'Splunk On-Call';

    public function deliverAlert(array $alert_data): bool
    {
        $url = $this->config['victorops-url'];
        $protocol = [
            'entity_id' => strval($alert_data['id'] ?: $alert_data['uid']),
            'state_start_time' => strtotime($alert_data['timestamp']),
            'entity_display_name' => $alert_data['title'],
            'state_message' => $alert_data['msg'],
            'monitoring_tool' => 'obzora',
        ];
        $protocol['message_type'] = match ($alert_data['state']) {
            AlertState::RECOVERED => 'RECOVERY',
            AlertState::ACKNOWLEDGED => 'ACKNOWLEDGEMENT',
            default => match ($alert_data['severity']) {
                'ok' => 'INFO',
                'warning' => 'WARNING',
                default => 'CRITICAL',
            },
        };

        foreach ($alert_data['faults'] as $fault => $data) {
            $protocol['state_message'] .= $data['string'];
        }

        $res = Http::client()->post($url, $protocol);

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
                    'title' => 'Post URL',
                    'name' => 'victorops-url',
                    'descr' => 'Victorops Post URL',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'victorops-url' => 'required|string',
            ],
        ];
    }
}
