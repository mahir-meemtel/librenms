<?php
namespace App\ApiClients;

use App\Facades\ObzoraConfig;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Log;

trait GeocodingHelper
{
    abstract protected function getClient(): PendingRequest;

    /**
     * Try to get the coordinates of a given address.
     * If unsuccessful, the returned array will be empty
     *
     * @param  string  $address
     * @return array ['lat' => 0, 'lng' => 0]
     */
    public function getCoordinates($address)
    {
        if (! ObzoraConfig::get('geoloc.latlng', true)) {
            Log::debug('Geocoding disabled');

            return [];
        }

        try {
            $client = $this->getClient()->withOptions($this->buildGeocodingOptions($address));

            $response = $client->get($this->geocoding_uri);
            $response_data = $response->json();
            if ($this->checkResponse($response, $response_data)) {
                return $this->parseLatLng($response_data);
            } else {
                Log::error('Geocoding failed.', ['response' => $response_data]);
            }
        } catch (Exception $e) {
            Log::error('Geocoding failed: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Checks if the request was a success
     */
    protected function checkResponse(Response $response, array $data): bool
    {
        return $response->successful();
    }

    /**
     * Get latitude and longitude from geocode response
     *
     * @param  array  $data
     * @return array
     */
    abstract protected function parseLatLng(array $data): array;

    /**
     * Build request option array
     *
     * @param  string  $address
     * @return array
     *
     * @throws Exception you may throw an Exception if validation fails
     */
    abstract protected function buildGeocodingOptions(string $address): array;
}
