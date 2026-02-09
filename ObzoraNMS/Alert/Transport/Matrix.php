<?php
namespace ObzoraNMS\Alert\Transport;

use App\View\SimpleTemplate;
use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Util\Http;

class Matrix extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        $server = $this->config['matrix-server'];
        $room = $this->config['matrix-room'];
        $authtoken = $this->config['matrix-authtoken'];
        $message = $this->config['matrix-message'];

        $txnid = rand(1111, 9999) . time();

        $server = preg_replace('/\/$/', '', $server);
        $host = $server . '/_matrix/client/r0/rooms/' . urlencode($room) . '/send/m.room.message/' . $txnid;

        $message = SimpleTemplate::parse($message, $alert_data);

        $body = ['body' => strip_tags($message), 'formatted_body' => "$message", 'msgtype' => 'm.notice', 'format' => 'org.matrix.custom.html'];

        $res = Http::client()
            ->withToken($authtoken)
            ->acceptJson()
            ->put($host, $body);

        if ($res->successful()) {
            return true;
        }

        throw new AlertTransportDeliveryException($alert_data, $res->status(), $res->body(), $message, $body);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Matrix-Server URL',
                    'name' => 'matrix-server',
                    'descr' => 'Matrix server URL up to the matrix api-part (for example: https://matrix.example.com/)',
                    'type' => 'text',
                ],
                [
                    'title' => 'Room',
                    'name' => 'matrix-room',
                    'descr' => 'Enter the room',
                    'type' => 'text',
                ],
                [
                    'title' => 'Auth_token',
                    'name' => 'matrix-authtoken',
                    'descr' => 'Enter the auth_token',
                    'type' => 'password',
                ],
                [
                    'title' => 'Message',
                    'name' => 'matrix-message',
                    'descr' => 'Enter the message',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'matrix-server' => 'required',
                'matrix-room' => 'required',
                'matrix-authtoken' => 'required',
            ],
        ];
    }
}
