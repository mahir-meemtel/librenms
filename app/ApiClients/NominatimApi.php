<?php
namespace App\ApiClients;

use ObzoraNMS\Interfaces\Geocoder;

class NominatimApi extends BaseApi implements Geocoder
{
    use GeocodingHelper;

    protected string $base_uri = 'https://nominatim.openstreetmap.org';
    protected string $geocoding_uri = '/search';

    /**
     * Get latitude and longitude from geocode response
     */
    protected function parseLatLng(array $data): array
    {
        return [
            'lat' => isset($data[0]['lat']) ? $data[0]['lat'] : 0,
            'lng' => isset($data[0]['lon']) ? $data[0]['lon'] : 0,
        ];
    }

    /**
     * Build request option array
     */
    protected function buildGeocodingOptions(string $address): array
    {
        return [
            'query' => [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
            ],
            'headers' => [
                'User-Agent' => 'ObzoraNMS',
                'Accept' => 'application/json',
            ],
        ];
    }
}
