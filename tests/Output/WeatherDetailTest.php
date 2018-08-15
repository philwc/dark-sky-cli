<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI\Output;

use philwc\DarkSky\Entity\DataPoint\CurrentlyDataPoint;
use philwc\DarkSky\Entity\DataPoint\DailyDataPoint;
use philwc\DarkSky\Entity\DataPoint\HourlyDataPoint;
use philwc\DarkSky\Entity\DataPoint\MinutelyDataPoint;
use philwc\DarkSky\Value\String\Units;
use PHPUnit\Framework\TestCase;

/**
 * Class WeatherDetailTest
 * @package philwc\DarkSky\CLI\Output
 * @covers \philwc\DarkSky\CLI\Output\WeatherDetail
 */
class WeatherDetailTest extends TestCase
{
    /**
     * @var WeatherDetail
     */
    private $instance;

    public function setUp()
    {
        $this->instance = new WeatherDetail();
    }

    public function testGetCurrentlyDetail()
    {
        $detail = $this->instance->getDetail(CurrentlyDataPoint::fromArray([
            'timezone' => new \DateTimeZone('Europe/London'),
            'units' => new Units('si'),
            'time' => time(),
            'temperature' => 15
        ]));

        $this->assertEquals([['Temperature', '15 °C']], $detail);
    }

    public function testGetMinutelyDetail()
    {
        $detail = $this->instance->getDetail(MinutelyDataPoint::fromArray([
            'timezone' => new \DateTimeZone('Europe/London'),
            'units' => new Units('si'),
            'time' => time(),
            'apparentTemperature' => 18
        ]));

        $this->assertEquals([['Apparent Temperature', '18 °C']], $detail);
    }

    public function testGetHourlyDetail()
    {
        $detail = $this->instance->getDetail(HourlyDataPoint::fromArray([
            'timezone' => new \DateTimeZone('Europe/London'),
            'units' => new Units('si'),
            'time' => time(),
            'precipAccumulation' => 15
        ]));

        $this->assertEquals([['Precipitation Accumulation', '15 cm']], $detail);
    }

    public function testGetDailyDetail()
    {
        $detail = $this->instance->getDetail(DailyDataPoint::fromArray([
            'timezone' => new \DateTimeZone('Europe/London'),
            'units' => new Units('si'),
            'time' => time(),
            'sunriseTime' => (new \DateTimeImmutable('2018-01-01 05:00'))->format('U')
        ]));

        $this->assertEquals([['Sunrise Time', 'Monday, 1st, January 2018 05:00:00']], $detail);
    }
}
