<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI\Output;

use philwc\DarkSky\Entity\DataPoint;
use philwc\DarkSky\Value\Value;

/**
 * Class WeatherDetail
 * @package philwc\DarkSky\CLI
 */
class WeatherDetail
{
    private const COMMON_METHODS = [
        'getCloudCover' => [],
        'getDewPoint' => [],
        'getHumidity' => [],
        'getOzone' => [],
        'getPressure' => [],
        'getUvIndex' => [],
        'getVisibility' => [],
        'getPrecipitation' => [
            'getIntensity',
            'getIntensityError',
            'getProbability',
            'getType',
        ],
        'getWind' => [
            'getBearing',
            'getGust',
            'getSpeed',
        ],
    ];

    private const CURRENTLY_METHODS = [
        'getNearestStorm' => [
            'getBearing',
            'getDistance',
        ],
        'getApparentTemperature' => [],
        'getTemperature' => [],
    ];

    private const MINUTELY_METHODS = [
        'getSummary' => [],
        'getIcon' => [],
        'getApparentTemperature' => [],
    ];

    private const HOURLY_METHODS = [
        'getSummary' => [],
        'getIcon' => [],
        'getPrecipAccumulation' => [],
        'getApparentTemperature' => [],
        'getTemperature' => [],
    ];

    private const DAILY_METHODS = [
        'getSummary' => [],
        'getIcon' => [],
        'getApparentTemperature' => [
            'getHigh',
            'getHighTime',
            'getLow',
            'getLowTime',
        ],
        'getMoonPhase' => [],
        'getPrecipIntensityMax' => [],
        'getPrecipIntensityMaxTime' => [],
        'getSunriseTime' => [],
        'getSunsetTime' => [],
        'getUvIndexTime' => [],
        'getWindGustTime' => [],
        'getPrecipAccumulation' => [],
        'getTemperature' => [
            'getHigh',
            'getHighTime',
            'getLow',
            'getLowTime',
        ],
    ];

    /**
     * @param DataPoint $dataPoint
     * @return array
     * @throws \Exception
     */
    public function getDetail(DataPoint $dataPoint): array
    {
        $detail = [];

        foreach ($this->getMethods($dataPoint) as $mainMethod => $subMethods) {
            if (empty($subMethods)) {
                if ($dataPoint->$mainMethod()) {
                    $detail[] = $this->handleValue($dataPoint->$mainMethod());
                }
                continue;
            }

            foreach ($subMethods as $subMethod) {
                if ($dataPoint->$mainMethod()->$subMethod()) {
                    $detail[] = $this->handleValue($dataPoint->$mainMethod()->$subMethod());
                }
            }
        }

        return $detail;
    }

    /**
     * @param $value
     * @return array
     * @throws \Exception
     */
    private function handleValue($value): array
    {
        if ($value instanceof Value) {
            return [$value->getTitle(), $value->toString()];
        }

        if (is_scalar($value)) {
            return ['', $value];
        }

        throw new \Exception('Cannot handle value with type ' . \get_class($value));
    }

    /**
     * @param DataPoint $dataPoint
     * @return array
     */
    private function getMethods(DataPoint $dataPoint): array
    {
        $methods = [];
        if ($dataPoint instanceof DataPoint\CurrentlyDataPoint) {
            $methods = self::CURRENTLY_METHODS;
        }

        if ($dataPoint instanceof DataPoint\MinutelyDataPoint) {
            $methods = self::MINUTELY_METHODS;
        }

        if ($dataPoint instanceof DataPoint\HourlyDataPoint) {
            $methods = self::HOURLY_METHODS;
        }

        if ($dataPoint instanceof DataPoint\DailyDataPoint) {
            $methods = self::DAILY_METHODS;
        }

        return array_merge($methods, self::COMMON_METHODS);
    }
}
