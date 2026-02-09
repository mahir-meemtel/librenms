<?php
namespace ObzoraNMS\Interfaces;

interface Geocoder
{
    /**
     * Try to get the coordinates of a given address.
     * If unsuccessful, the returned array will be empty
     *
     * @param  string  $address
     * @return array ['lat' => 0, 'lng' => 0]
     */
    public function getCoordinates($address);
}
