<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Enum\AlertState;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Gitlab extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        // Don't create tickets for resolutions
        if ($alert_data['state'] == AlertState::RECOVERED) {
            return true;
        }

        $project_id = $this->config['gitlab-id'];
        $url = $this->config['gitlab-host'] . "/api/v4/projects/$project_id/issues";
        $data = [
            'title' => 'Obzora alert for: ' . $alert_data['hostname'],
            'description' => $alert_data['msg'],
        ];

        $res = Http::client()
            ->withHeaders([
                'PRIVATE-TOKEN' => $this->config['gitlab-key'],
            ])
            ->acceptJson()
            ->post($url, $data);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $data['description'], $data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Host',
                    'name' => 'gitlab-host',
                    'descr' => 'GitLab Host',
                    'type' => 'text',
                ],
                [
                    'title' => 'Project ID',
                    'name' => 'gitlab-id',
                    'descr' => 'GitLab Project ID',
                    'type' => 'text',
                ],
                [
                    'title' => 'Personal Access Token',
                    'name' => 'gitlab-key',
                    'descr' => 'Personal Access Token',
                    'type' => 'password',
                ],
            ],
            'validation' => [
                'gitlab-host' => 'required|string',
                'gitlab-id' => 'required|string',
                'gitlab-key' => 'required|string',
            ],
        ];
    }
}
