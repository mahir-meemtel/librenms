<?php
namespace ObzoraNMS\Alert\Transport;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;

class Irc extends Transport
{
    protected string $name = 'IRC';

    public function deliverAlert(array $alert_data): bool
    {
        $container_dir = '/data';
        if (file_exists($container_dir) and posix_getpwuid(fileowner($container_dir))['name'] == 'obzora') {
            $f = $container_dir . '/.ircbot.alert';
        } else {
            $f = ObzoraConfig::get('install_dir') . '/.ircbot.alert';
        }
        if (file_exists($f) && filetype($f) == 'fifo') {
            $f = fopen($f, 'w+');
            $r = fwrite($f, json_encode($alert_data) . "\n");
            fclose($f);

            if ($r === false) {
                throw new AlertTransportDeliveryException($alert_data, 0, 'Could not write to fifo', $alert_data['msg'], $alert_data);
            }

            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, 0, 'fifo does not exist', $alert_data['msg'], $alert_data);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'IRC',
                    'name' => 'irc',
                    'descr' => 'Enable IRC alerts',
                    'type' => 'checkbox',
                    'default' => true,
                ],
            ],
            'validation' => [
                'irc' => 'required',
            ],
        ];
    }
}
