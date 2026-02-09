<?php
namespace App\ApiClients;

use ObzoraNMS\Util\Http;

class BaseApi
{
    protected string $base_uri = '';
    protected int $timeout = 3;
    private ?\Illuminate\Http\Client\PendingRequest $client = null;

    protected function getClient(): \Illuminate\Http\Client\PendingRequest
    {
        if (is_null($this->client)) {
            $this->client = Http::client()->baseUrl($this->base_uri)
            ->timeout($this->timeout);
        }

        return $this->client;
    }
}
