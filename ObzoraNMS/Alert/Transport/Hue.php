<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

/**
 * The Hue API currently is fairly limited for alerts.
 * At it's current implementation we can send ['lselect' => "15 second flash", 'select' => "1 second flash"]
 * If a colour request is sent with it it will permenantly change the colour which is less than desired
 */
class Hue extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        // Don't alert on resolve at this time
        if ($alert_data['state'] == AlertState::RECOVERED) {
            return true;
        }

        $hue_user = $this->config['hue-user'];
        $url = $this->config['hue-host'] . "/api/$hue_user/groups/0/action";
        $duration = $this->config['hue-duration'];
        $data = ['alert' => $duration];

        $res = Http::client()
            ->acceptJson()
            ->put($url, $data);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $duration, $data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Host',
                    'name' => 'hue-host',
                    'descr' => 'Hue Host',
                    'type' => 'text',
                ],
                [
                    'title' => 'Hue User',
                    'name' => 'hue-user',
                    'descr' => 'Phillips Hue Host',
                    'type' => 'text',
                ],
                [
                    'title' => 'Duration',
                    'name' => 'hue-duration',
                    'descr' => 'Phillips Hue Duration',
                    'type' => 'select',
                    'options' => [
                        '1 Second' => 'select',
                        '15 Seconds' => 'lselect',
                    ],
                ],
            ],
            'validation' => [
                'hue-host' => 'required|string',
                'hue-user' => 'required|string',
                'hue-duration' => 'required|string',
            ],
        ];
    }
}
