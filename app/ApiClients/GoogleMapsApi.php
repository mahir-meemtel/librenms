<?php
namespace App\ApiClients;

use App\Facades\ObzoraConfig;
use Exception;
use Illuminate\Http\Client\Response;
use ObzoraNMS\Interfaces\Geocoder;

class GoogleMapsApi extends BaseApi implements Geocoder
{
    use GeocodingHelper;

    protected string $base_uri = 'https://maps.googleapis.com';
    protected string $geocoding_uri = '/maps/api/geocode/json';

    /**
     * Get latitude and longitude from geocode response
     */
    protected function parseLatLng(array $data): array
    {
        return [
            'lat' => isset($data['results'][0]['geometry']['location']['lat']) ? $data['results'][0]['geometry']['location']['lat'] : 0,
            'lng' => isset($data['results'][0]['geometry']['location']['lng']) ? $data['results'][0]['geometry']['location']['lng'] : 0,
        ];
    }

    /**
     * Get messages from response.
     */
    protected function parseMessages(array $data): array
    {
        return [
            'error' => isset($data['error_message']) ? $data['error_message'] : '',
            'response' => $data,
        ];
    }

    /**
     * Build request option array
     *
     * @throws Exception you may throw an Exception if validation fails
     */
    protected function buildGeocodingOptions(string $address): array
    {
        $api_key = ObzoraConfig::get('geoloc.api_key');
        if (! $api_key) {
            throw new Exception('Google Maps API key missing, set geoloc.api_key');
        }

        return [
            'query' => [
                'key' => $api_key,
                'address' => $address,
            ],
        ];
    }

    /**
     * Checks if the request was a success
     */
    protected function checkResponse(Response $response, array $data): bool
    {
        return $response->successful() && $data['status'] == 'OK';
    }
}
