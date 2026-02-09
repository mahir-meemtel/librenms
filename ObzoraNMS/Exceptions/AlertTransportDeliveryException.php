<?php
namespace ObzoraNMS\Exceptions;

class AlertTransportDeliveryException extends \Exception
{
    public function __construct(
        array $data,
        int $code = 0,
        protected string $response = '',
        protected string $template = '',
        protected array $params = []
    ) {
        $name = $data['transport_name'] ?? '';

        $message = "Transport delivery failed with $code for $name: $response";

        parent::__construct($message, $code);
    }
}
