<?php
namespace App\ApiClients;

use ObzoraNMS\Exceptions\ApiClientException;

class RipeApi extends BaseApi
{
    protected string $base_uri = 'https://stat.ripe.net';

    protected string $whois_uri = '/data/whois/data.json';
    protected string $abuse_uri = '/data/abuse-contact-finder/data.json';

    /**
     * Get whois info
     *
     * @throws ApiClientException
     */
    public function getWhois(string $resource): array
    {
        return $this->makeApiCall($this->whois_uri, [
            'query' => [
                'resource' => $resource,
            ],
        ]);
    }

    /**
     * Get Abuse contact
     *
     * @throws ApiClientException
     */
    public function getAbuseContact(string $resource): mixed
    {
        return $this->makeApiCall($this->abuse_uri, [
            'query' => [
                'resource' => $resource,
            ],
        ]);
    }

    /**
     * @throws ApiClientException
     */
    private function makeApiCall(string $uri, array $options): mixed
    {
        $response_data = $this->getClient()->get($uri, $options['query'])->json();

        if (isset($response_data['status']) && $response_data['status'] == 'ok') {
            return $response_data;
        }

        throw new ApiClientException("RIPE API call to $this->base_uri/$uri failed: " . $this->getClient()->get($uri, $options['query'])->status(), $response_data);
    }
}
