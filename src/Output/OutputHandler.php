<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI\Output;

use philwc\DarkSky\Entity\Alert;
use philwc\DarkSky\Entity\DataPoint\HourlyDataPoint;
use philwc\DarkSky\Entity\DataPoint\MinutelyDataPoint;
use philwc\DarkSky\Entity\Request;
use philwc\DarkSky\Entity\Weather;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class OutputHandlers
 * @package philwc\DarkSky\CLI\Output
 */
class OutputHandler
{
    /**
     * @var WeatherDetail
     */
    private $weatherDetail;

    /**
     * OutputHandlers constructor.
     * @param WeatherDetail $weatherDetail
     */
    public function __construct(WeatherDetail $weatherDetail)
    {
        $this->weatherDetail = $weatherDetail;
    }

    /**
     * @param Weather $weather
     * @param Request $request
     * @param InputInterface $input
     * @param SymfonyStyle $style
     * @throws \Exception
     */
    public function handleAll(Weather $weather, Request $request, InputInterface $input, SymfonyStyle $style): void
    {
        $this->handleTitle($weather, $request, $input, $style);
        $this->handleAlerts($weather, $style);
        $this->handleCurrently($weather, $input, $style);
        $this->handleMinutely($weather, $input, $style);
        $this->handleHourly($weather, $input, $style);
        $this->handleDaily($weather, $input, $style);
    }

    /**
     * @param Weather $weather
     * @param Request $request
     * @param InputInterface $input
     * @param SymfonyStyle $style
     */
    public function handleTitle(Weather $weather, Request $request, InputInterface $input, SymfonyStyle $style): void
    {
        $style->title('Weather for ' . $request->getLocationDescription());

        $hasOptions = array_filter($input->getOptions()) !== [];

        if ($hasOptions === false && $weather->getCurrently()) {
            $style->section(
                $weather->getCurrently()->getTime()->toString() .
                ' (' . $weather->getTimezone()->getName() . ')'
            );
            $style->writeln(
                'Currently ' . $weather->getCurrently()->getSummary()->toString() . ' ' .
                $weather->getCurrently()->getIcon()->getIcon()
            );
        }
    }

    /**
     * @param Weather $weather
     * @param SymfonyStyle $style
     */
    public function handleAlerts(Weather $weather, SymfonyStyle $style): void
    {
        if ($weather->getAlerts()) {
            /** @var Alert $alert */
            foreach ($weather->getAlerts() as $alert) {
                $description = trim(
                    str_replace(
                        '...',
                        ' ',
                        preg_replace('/\s+\*\s+/', PHP_EOL, $alert->getDescription())
                    )
                );

                $style->warning(
                    $alert->getTitle() . '(' . $alert->getTime()->toString() . ')' . PHP_EOL . PHP_EOL . $description
                );
            }
        }
    }

    /**
     * @param Weather $weather
     * @param InputInterface $input
     * @param SymfonyStyle $style
     * @throws \Exception
     */
    public function handleCurrently(Weather $weather, InputInterface $input, SymfonyStyle $style): void
    {
        $currently = $weather->getCurrently();

        if ($currently && ($input->getOption('currently') || $input->getOption('currentlyDetail'))) {
            $style->writeln(
                'Currently ' . $currently->getSummary()->toString() . ' ' . $currently->getIcon()->getIcon()
            );
            $style->writeln(
                $weather->getCurrently()->getTime()->toString() .
                ' (' . $weather->getTimezone()->getName() . ')' . PHP_EOL
            );

            if ($input->getOption('currentlyDetail')) {
                $style->writeln('Details');
                $style->table([], $this->weatherDetail->getDetail($currently));
            }
        }
    }

    /**
     * @param Weather $weather
     * @param InputInterface $input
     * @param SymfonyStyle $style
     * @throws \Exception
     */
    public function handleMinutely(Weather $weather, InputInterface $input, SymfonyStyle $style): void
    {
        $minutely = $weather->getMinutely();
        if ($minutely && ($input->getOption('minutely') || $input->getOption('minutelyDetail'))) {
            $style->writeln($minutely->getSummary()->toString() . ' ' . $minutely->getIcon()->getIcon());

            if ($input->getOption('minutelyDetail')) {
                $style->writeln('Details');
                /** @var MinutelyDataPoint $item */
                foreach ($minutely->getData() as $item) {
                    $style->section($item->getTime()->toString() . ' (' . $weather->getTimezone()->getName() . ')');
                    $style->table([], $this->weatherDetail->getDetail($item));
                }
            }
        }
    }

    /**
     * @param Weather $weather
     * @param InputInterface $input
     * @param SymfonyStyle $style
     * @throws \Exception
     */
    public function handleHourly(Weather $weather, InputInterface $input, SymfonyStyle $style): void
    {
        $hourly = $weather->getHourly();
        if ($hourly && ($input->getOption('hourly') || $input->getOption('hourlyDetail'))) {
            $style->writeln($hourly->getSummary()->toString() . ' ' . $hourly->getIcon()->getIcon());

            if ($input->getOption('hourlyDetail')) {
                $style->writeln('Details');
                /** @var HourlyDataPoint $item */
                foreach ($hourly->getData() as $item) {
                    $style->section($item->getTime()->toString() . ' (' . $weather->getTimezone()->getName() . ')');
                    $style->table([], $this->weatherDetail->getDetail($item));
                }
            }
        }
    }

    /**
     * @param Weather $weather
     * @param InputInterface $input
     * @param SymfonyStyle $style
     * @throws \Exception
     */
    public function handleDaily(Weather $weather, InputInterface $input, SymfonyStyle $style): void
    {
        $daily = $weather->getDaily();
        if ($daily && ($input->getOption('daily') || $input->getOption('dailyDetail'))) {
            $style->writeln($daily->getSummary()->toString() . ' ' . $daily->getIcon()->getIcon());

            if ($input->getOption('dailyDetail')) {
                $style->writeln('Details');
                /** @var HourlyDataPoint $item */
                foreach ($daily->getData() as $item) {
                    $style->section($item->getTime()->toString() . ' (' . $weather->getTimezone()->getName() . ')');
                    $style->table([], $this->weatherDetail->getDetail($item));
                }
            }
        }
    }
}
