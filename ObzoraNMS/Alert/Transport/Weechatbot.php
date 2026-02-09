<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;

class WeechatBot extends Transport
{
    protected string $name = 'Weechat Bot';

    public function deliverAlert(array $alert_data): bool
    {
        $pre = "{$this->config['bot-password']} ";
        if (isset($this->config['irc-server']) && strlen($this->config['irc-server']) !== 0) {
            $pre .= "{$this->config['irc-server']} ";
        }
        $pre .= "{$this->config['irc-channel']} ";

        try {
            $fp = (new \Socket\Raw\Factory())
                    ->createClient("udp://{$this->config['bot-hostname']}:{$this->config['bot-port']}");
        } catch (\Socket\Raw\Exception $e) {
            throw new AlertTransportDeliveryException($alert_data, $e->getCode(), $e->getMessage());
        }

        $fp->write($pre . $alert_data['title']);
        foreach (preg_split('/((\r?\n)|(\r\n?))/', $alert_data['msg']) as $line) {
            $fp->write($pre . $line);
        }
        $fp->close();

        return true;
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Weechat Bot server',
                    'name' => 'bot-hostname',
                    'descr' => 'hostname or IP address of Weechat Bot server',
                    'type' => 'text',
                ],
                [
                    'title' => 'Weechat Bot port',
                    'name' => 'bot-port',
                    'descr' => 'port Weechat Bot server listens',
                    'type' => 'text',
                ],
                [
                    'title' => 'UDP listener Password',
                    'name' => 'bot-password',
                    'descr' => 'Weechat Bot UDP listener password',
                    'type' => 'password',
                ],
                [
                    'title' => 'IRC server',
                    'name' => 'irc-server',
                    'descr' => 'IRC server to send alert',
                    'type' => 'text',
                ],
                [
                    'title' => 'IRC channel',
                    'name' => 'irc-channel',
                    'descr' => 'IRC channel to send alert',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'bot-hostname' => 'required|string',
                'bot-port' => 'required|integer|between:1,65535',
                'bot-password' => 'required|string',
                'irc-server' => 'string',
                'irc-channel' => 'required|string',
            ],
        ];
    }
}
