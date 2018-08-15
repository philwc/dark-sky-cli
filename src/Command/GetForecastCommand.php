<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI\Command;

use philwc\DarkSky\CLI\Output\OutputHandler;
use philwc\DarkSky\CLI\RequestCollectionFactory;
use philwc\DarkSky\CLI\SecretStore;
use philwc\DarkSky\ClientFactory;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GetForecastCommand
 * @package philwc\DarkSky\CLI\Command
 */
class GetForecastCommand extends Command
{
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var SecretStore
     */
    private $secretStore;
    /**
     * @var RequestCollectionFactory
     */
    private $requestCollectionFactory;
    /**
     * @var OutputHandler
     */
    private $outputHandler;

    /**
     * GenerateTokenCommand constructor.
     * @param SecretStore $secretStore
     * @param CacheInterface $cache
     * @param RequestCollectionFactory $requestCollectionFactory
     * @param OutputHandler $outputHandler
     */
    public function __construct(
        SecretStore $secretStore,
        CacheInterface $cache,
        RequestCollectionFactory $requestCollectionFactory,
        OutputHandler $outputHandler
    ) {
        parent::__construct(null);
        $this->secretStore = $secretStore;
        $this->cache = $cache;
        $this->requestCollectionFactory = $requestCollectionFactory;
        $this->outputHandler = $outputHandler;
    }

    /**
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setName('forecast:location')
            ->addArgument(
                'locations',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'The locations to get weather information for'
            )
            ->addOption(
                'currently',
                null,
                InputOption::VALUE_NONE,
                'Whether to show currently information'
            )
            ->addOption(
                'currentlyDetail',
                null,
                InputOption::VALUE_NONE,
                'Whether to show currently detailed information'
            )
            ->addOption(
                'minutely',
                null,
                InputOption::VALUE_NONE,
                'Whether to show minutely information'
            )
            ->addOption(
                'minutelyDetail',
                null,
                InputOption::VALUE_NONE,
                'Whether to show minutely detailed information'
            )
            ->addOption(
                'hourly',
                null,
                InputOption::VALUE_NONE,
                'Whether to show hourly information'
            )
            ->addOption(
                'hourlyDetail',
                null,
                InputOption::VALUE_NONE,
                'Whether to show hourly detailed information'
            )
            ->addOption(
                'daily',
                null,
                InputOption::VALUE_NONE,
                'Whether to show hourly information'
            )
            ->addOption(
                'dailyDetail',
                null,
                InputOption::VALUE_NONE,
                'Whether to show hourly detailed information'
            )
            ->setDescription(
                'Get the weather forecast for the locations specified.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Assert\AssertionFailedException
     * @throws \Geocoder\Exception\Exception
     * @throws \philwc\DarkSky\Exception\MissingDataException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $client = ClientFactory::get($this->secretStore->get(), $this->cache);

        $requestCollection = $this->requestCollectionFactory->getRequestCollection(
            $input->getArgument('locations')
        );

        $weatherCollection = $client->retrieve($requestCollection);

        foreach ($weatherCollection as $key => $weather) {
            $this->outputHandler->handleAll(
                $weather,
                $requestCollection->get($key),
                $input,
                $style
            );
        }
    }
}
