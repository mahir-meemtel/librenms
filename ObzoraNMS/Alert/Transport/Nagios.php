<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;

class Nagios extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        /*
         host_perfdata_file_template=
         [HOSTPERFDATA]\t
         $TIMET$\t
         $HOSTNAME$\t
         HOST\t
         $HOSTSTATE$\t
         $HOSTEXECUTIONTIME$\t
         $HOSTLATENCY$\t
         $HOSTOUTPUT$\t
         $HOSTPERFDATA$
         */

        $format = '';
        $format .= "[HOSTPERFDATA]\t";
        $format .= strtotime($alert_data['timestamp']) . "\t";
        $format .= $alert_data['hostname'] . "\t";
        $format .= md5($alert_data['rule']) . "\t"; //FIXME: Better entity
        $format .= ($alert_data['state'] ? $alert_data['severity'] : 'ok') . "\t";
        $format .= "0\t";
        $format .= "0\t";
        $format .= str_replace("\n", '', nl2br($alert_data['msg'])) . "\t";
        $format .= 'NULL'; //FIXME: What's the HOSTPERFDATA equivalent for ObzoraNMS? Oo
        $format .= "\n";

        $fifo = $this->config['nagios-fifo'];
        if (filetype($fifo) !== 'fifo') {
            throw new AlertTransportDeliveryException($alert_data, 0, 'File is not a fifo file! Refused to write to it.');
        }

        return file_put_contents($fifo, $format);
    }

    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'Nagios FIFO',
                    'name' => 'nagios-fifo',
                    'descr' => 'Nagios compatible FIFO',
                    'type' => 'text',
                ],
            ],
            'validation' => [
                'nagios-fifo' => 'required',
            ],
        ];
    }
}
