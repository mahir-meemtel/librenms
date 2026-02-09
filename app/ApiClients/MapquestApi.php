<?php
namespace App\ApiClients;

use App\Facades\ObzoraConfig;
use Exception;
use Illuminate\Http\Client\Response;
use ObzoraNMS\Interfaces\Geocoder;

class MapquestApi extends BaseApi implements Geocoder
{
    use GeocodingHelper;

    protected string $base_uri = 'https://open.mapquestapi.com';
    protected string $geocoding_uri = '/geocoding/v1/address';

    /**
     * Get latitude and longitude from geocode response
     */
    protected function parseLatLng(array $data): array
    {
        return [
            'lat' => isset($data['results'][0]['locations'][0]['latLng']['lat']) ? $data['results'][0]['locations'][0]['latLng']['lat'] : 0,
            'lng' => isset($data['results'][0]['locations'][0]['latLng']['lng']) ? $data['results'][0]['locations'][0]['latLng']['lng'] : 0,
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
            throw new Exception('MapQuest API key missing, set geoloc.api_key');
        }

        return [
            'query' => [
                'key' => $api_key,
                'location' => $address,
                'thumbMaps' => 'false',
            ],
        ];
    }

    /**
     * Checks if the request was a success
     */
    protected function checkResponse(Response $response, array $data): bool
    {
        return $response->successful() && $data['info']['statuscode'] == 0;
    }
}
