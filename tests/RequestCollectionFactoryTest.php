<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI;

use Geocoder\Formatter\StringFormatter;
use philwc\DarkSky\Entity\ForecastRequest;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * Class RequestCollectionFactoryTest
 * @package philwc\DarkSky\CLI
 * @covers \philwc\DarkSky\CLI\RequestCollectionFactory
 */
class RequestCollectionFactoryTest extends TestCase
{
    public function testForecastRequestCollectionFactory()
    {
        $requestCollectionFactory = new RequestCollectionFactory(new NullGeocoder(), new StringFormatter());
        $requestCollectionFactory->setLogger(new NullLogger());

        $requestCollection = $requestCollectionFactory->getRequestCollection(['Test']);

        $this->assertEquals(1, $requestCollection->count());
        /** @var ForecastRequest $request */
        $request = $requestCollection->first();

        $this->assertEquals(2.2426, $request->getLongitude()->toFloat());
        $this->assertEquals(53.4808, $request->getLatitude()->toFloat());
    }

    public function testTimeMachineRequestCollectionFactory()
    {
        $requestCollectionFactory = new RequestCollectionFactory(new NullGeocoder(), new StringFormatter());
        $requestCollectionFactory->setLogger(new NullLogger());

        $requestCollection = $requestCollectionFactory->getRequestCollection(
            ['Test'],
            new \DateTimeImmutable('2018-01-01 00:00:00')
        );

        $this->assertEquals(1, $requestCollection->count());
        /** @var ForecastRequest $request */
        $request = $requestCollection->first();

        $this->assertEquals(2.2426, $request->getLongitude()->toFloat());
        $this->assertEquals(53.4808, $request->getLatitude()->toFloat());
    }
}
