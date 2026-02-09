<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;

class Signal extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        exec(escapeshellarg($this->config['path'])
           . ' --dbus-system send'
           . (($this->config['recipient-type'] == 'group') ? ' -g ' : ' ')
           . escapeshellarg($this->config['recipient'])
           . ' -m ' . escapeshellarg($alert_data['title']));

        return true;
    }

    public static function configTemplate(): array
    {
        return [
            'validation' => [],
            'config' => [
                [
                    'title' => 'Path',
                    'name' => 'path',
                    'descr' => 'Local Path to CLI',
                    'type' => 'text',
                ],
                [
                    'title' => 'Recipient type',
                    'name' => 'recipient-type',
                    'descr' => 'Phonenumber ',
                    'type' => 'select',
                    'options' => [
                        'Mobile number' => 'single',
                        'Group' => 'group',
                    ],
                ],
                [
                    'title' => 'Recipient',
                    'name' => 'recipient',
                    'descr' => 'Message recipient',
                    'type' => 'text',
                ],
            ],
        ];
    }
}
