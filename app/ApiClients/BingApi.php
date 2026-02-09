<?php
namespace App\ApiClients;

use App\Facades\ObzoraConfig;
use Exception;
use Illuminate\Http\Client\Response;
use ObzoraNMS\Interfaces\Geocoder;

class BingApi extends BaseApi implements Geocoder
{
    use GeocodingHelper;

    protected string $base_uri = 'http://dev.virtualearth.net';
    protected string $geocoding_uri = '/REST/v1/Locations';

    /**
     * Get latitude and longitude from geocode response
     */
    protected function parseLatLng(array $data): array
    {
        return [
            'lat' => isset($data['resourceSets'][0]['resources'][0]['point']['coordinates'][0]) ? $data['resourceSets'][0]['resources'][0]['point']['coordinates'][0] : 0,
            'lng' => isset($data['resourceSets'][0]['resources'][0]['point']['coordinates'][1]) ? $data['resourceSets'][0]['resources'][0]['point']['coordinates'][1] : 0,
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
            throw new Exception('Bing API key missing, set geoloc.api_key');
        }

        return [
            'query' => [
                'key' => $api_key,
                'addressLine' => $address,
            ],
        ];
    }

    /**
     * Checks if the request was a success
     */
    protected function checkResponse(Response $response, array $data): bool
    {
        return $response->successful() && ! empty($data['resourceSets'][0]['resources']);
    }
}
