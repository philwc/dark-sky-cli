<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI;

use Geocoder\Collection;
use Geocoder\Geocoder;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\AdminLevel;
use Geocoder\Model\AdminLevelCollection;
use Geocoder\Model\Coordinates;
use Geocoder\Provider\Nominatim\Model\NominatimAddress;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

class NullGeocoder implements Geocoder
{
    /**
     * Geocodes a given value.
     *
     * @param string $value
     *
     * @return Collection
     *
     * @throws \Geocoder\Exception\Exception
     */
    public function geocode(string $value): Collection
    {
        return new AddressCollection();
    }

    /**
     * Reverses geocode given latitude and longitude values.
     *
     * @param float $latitude
     * @param float $longitude
     *
     * @return Collection
     *
     * @throws \Geocoder\Exception\Exception
     */
    public function reverse(float $latitude, float $longitude): Collection
    {
        return new AddressCollection();
    }

    /**
     * @param GeocodeQuery $query
     *
     * @return Collection
     *
     * @throws \Geocoder\Exception\Exception
     */
    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        $adminLevelCollection = new AdminLevelCollection([
            new AdminLevel(1, 'Test')
        ]);

        $coordinates = new Coordinates(53.4808, 2.2426);

        $addressCollection = new AddressCollection([
            new Address(
                $this->getName(),
                $adminLevelCollection,
                $coordinates
            ),
        ]);

        return $addressCollection;
    }

    /**
     * @param ReverseQuery $query
     *
     * @return Collection
     *
     * @throws \Geocoder\Exception\Exception
     */
    public function reverseQuery(ReverseQuery $query): Collection
    {
        return new AddressCollection();
    }

    /**
     * Returns the provider's name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'NullGeocoder';
    }
}