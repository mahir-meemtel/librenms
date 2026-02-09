<?php
namespace App\Http\Controllers\Ajax;

use Illuminate\Http\JsonResponse;

class SnmpCapabilities
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'auth' => \ObzoraNMS\SNMPCapabilities::supportedAuthAlgorithms(),
            'crypto' => \ObzoraNMS\SNMPCapabilities::supportedCryptoAlgorithms(),
        ]);
    }
}
