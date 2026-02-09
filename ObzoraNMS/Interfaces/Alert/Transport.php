<?php
namespace ObzoraNMS\Interfaces\Alert;

interface Transport
{
    /**
     * @return string The display name of this transport.
     */
    public function name(): string;

    /**
     * Gets called when an alert is sent
     *
     * @param  array  $alert_data  An array created by DescribeAlert
     * @return bool Returns true if the call was successful.
     *
     * @throws \ObzoraNMS\Exceptions\AlertTransportDeliveryException
     */
    public function deliverAlert(array $alert_data): bool;

    /**
     * @return array
     */
    public static function configTemplate(): array;

    /**
     * Display the configuration details of this alert transport
     *
     * @return string
     */
    public function displayDetails(): string;
}
