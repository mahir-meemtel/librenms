<?php
namespace App\Models;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ObzoraNMS\Util\Dns;

/**
 * @method static \Database\Factories\LocationFactory factory(...$parameters)
 */
class Location extends Model
{
    use HasFactory;

    public $fillable = ['location', 'lat', 'lng'];
    const CREATED_AT = null;
    const UPDATED_AT = 'timestamp';

    private $location_regex = '/\[\s*(?<lat>[-+]?(?:[1-8]?\d(?:\.\d+)?|90(?:\.0+)?))\s*,\s*(?<lng>[-+]?(?:180(?:\.0+)?|(?:(?:1[0-7]\d)|(?:[1-9]?\d))(?:\.\d+)?))\s*\]/';
    private $location_ignore_regex = '/\(.*?\)/';

    /**
     * @return array{lat: 'float', lng: 'float', fixed_coordinates: 'bool'}
     */
    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'fixed_coordinates' => 'bool',
        ];
    }

    // ---- Helper Functions ----

    /**
     * Checks if this location has resolved latitude and longitude.
     *
     * @return bool
     */
    public function hasCoordinates()
    {
        return ! (is_null($this->lat) || is_null($this->lng));
    }

    /**
     * Check if the coordinates are valid
     * Even though 0,0 is a valid coordinate, we consider it invalid for ease
     */
    public function coordinatesValid()
    {
        return $this->lat && $this->lng &&
            abs($this->lat) <= 90 && abs($this->lng) <= 180;
    }

    /**
     * Try to parse coordinates
     * then try to lookup DNS LOC records if hostname is provided
     * then call geocoding API to resolve latitude and longitude.
     *
     * @param  string  $hostname
     * @return bool
     */
    public function lookupCoordinates($hostname = null)
    {
        if ($this->location && $this->parseCoordinates()) {
            return true;
        }

        if ($hostname && \App\Facades\ObzoraConfig::get('geoloc.dns')) {
            $coord = app(Dns::class)->getCoordinates($hostname);

            if (! empty($coord)) {
                $this->fill($coord);

                return true;
            }
        }

        if ($this->location && ! $this->hasCoordinates() && \App\Facades\ObzoraConfig::get('geoloc.latlng', true)) {
            return $this->fetchCoordinates();
        }

        return false;
    }

    /**
     * Remove encoded GPS for nicer display
     *
     * @param  bool  $withCoords
     * @return string
     */
    public function display($withCoords = false)
    {
        return (trim(preg_replace($this->location_regex, '', $this->location)) ?: $this->location)
            . ($withCoords && $this->coordinatesValid() ? " [$this->lat,$this->lng]" : '');
    }

    protected function parseCoordinates()
    {
        if (preg_match($this->location_regex, $this->location, $parsed)) {
            $this->fill($parsed);

            return true;
        }

        return false;
    }

    protected function fetchCoordinates()
    {
        try {
            /** @var \ObzoraNMS\Interfaces\Geocoder $api */
            $api = app(\ObzoraNMS\Interfaces\Geocoder::class);

            // Removes Location info inside () when looking up lat/lng
            $this->fill($api->getCoordinates(preg_replace($this->location_ignore_regex, '', $this->location)));

            return true;
        } catch (BindingResolutionException $e) {
            // could not resolve geocoder, Laravel isn't booted. Fail silently.
        }

        return false;
    }

    // ---- Query scopes ----

    /**
     * @param  Builder  $query
     * @param  User  $user
     * @return Builder
     */
    public function scopeHasAccess($query, $user)
    {
        if ($user->hasGlobalRead()) {
            return $query;
        }

        $ids = Device::hasAccess($user)
            ->distinct()
            ->whereNotNull('location_id')
            ->pluck('location_id');

        return $query->whereIntegerInRaw('id', $ids);
    }

    public function scopeInDeviceGroup($query, $deviceGroup)
    {
        return $query->whereHas('devices.groups', function ($query) use ($deviceGroup) {
            $query->where('device_groups.id', $deviceGroup);
        });
    }

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Device, $this>
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'location_id');
    }

    public function __toString()
    {
        return $this->location;
    }
}
