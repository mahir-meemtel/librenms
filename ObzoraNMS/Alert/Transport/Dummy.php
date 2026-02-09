<?php
namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;

class Dummy extends Transport
{
    public function deliverAlert(array $alert_data): bool
    {
        throw new AlertTransportDeliveryException($alert_data, 0, 'Dummy transport always fails', $alert_data['msg']);
    }

    public static function configTemplate(): array
    {
        return [
            'validation' => [],
            'config' => [],
        ];
    }
}
