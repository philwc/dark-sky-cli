<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI;

use Geocoder\Formatter\StringFormatter;
use Geocoder\Geocoder;
use Geocoder\Provider\Nominatim\Model\NominatimAddress;
use Geocoder\Query\GeocodeQuery;
use philwc\DarkSky\Entity\ForecastRequest;
use philwc\DarkSky\Entity\TimeMachineRequest;
use philwc\DarkSky\EntityCollection\RequestCollection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Class RequestCollectionFactory
 * @package philwc\DarkSky\CLI
 */
class RequestCollectionFactory implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var Geocoder
     */
    private $geocoder;
    /**
     * @var StringFormatter
     */
    private $formatter;

    /**
     * RequestCollectionFactory constructor.
     * @param Geocoder $geocoder
     * @param StringFormatter $formatter
     */
    public function __construct(Geocoder $geocoder, StringFormatter $formatter)
    {
        $this->geocoder = $geocoder;
        $this->formatter = $formatter;
    }

    /**
     * @param array $locations
     * @param \DateTimeImmutable|null $dateTime
     * @return RequestCollection
     * @throws \Assert\AssertionFailedException
     * @throws \Geocoder\Exception\Exception
     * @throws \philwc\DarkSky\Exception\MissingDataException
     */
    public function getRequestCollection(array $locations, \DateTimeImmutable $dateTime = null): RequestCollection
    {
        $requestCollection = new RequestCollection();

        foreach ($locations as $location) {
            $result = $this->geocoder->geocodeQuery(GeocodeQuery::create($location));
            if ($result->isEmpty()) {
                $this->logger->error('Unable to find country ' . $location);
            }
            /** @var NominatimAddress $queryLocation */
            $queryLocation = $result->first();

            $coordinates = $queryLocation->getCoordinates();

            if (!$coordinates) {
                $this->logger->error('No coordinates returned from remote service for location ' . $location);
            }

            $locationDescription = $this->formatter->format($queryLocation, '%L, %A1, %C');

            if ($dateTime === null) {
                $request = ForecastRequest::fromArray([
                    'latitude' => $coordinates->getLatitude(),
                    'longitude' => $coordinates->getLongitude(),
                    'parameters' => ['lang' => 'en', 'units' => 'si'],
                    'locationdescription' => $locationDescription,
                ]);
            } else {
                $request = TimeMachineRequest::fromArray([
                    'latitude' => $coordinates->getLatitude(),
                    'longitude' => $coordinates->getLongitude(),
                    'parameters' => ['lang' => 'en', 'units' => 'si'],
                    'locationdescription' => $locationDescription,
                    'datetime' => $dateTime
                ]);
            }

            $requestCollection->add($request);
        }

        return $requestCollection;
    }
}
