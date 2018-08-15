<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI;

use Auryn\Injector;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use Geocoder\Geocoder;
use Geocoder\Provider\Cache\ProviderCache;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\StatefulGeocoder;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use philwc\DarkSky\CLI\Command\GetForecastCommand;
use philwc\DarkSky\CLI\Command\GetHistoryCommand;
use philwc\DarkSky\CLI\Command\SetSecretCommand;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Application;

/**
 * Class Bootstrapper
 * @package philwc\DarkSky\CLI
 */
class Bootstrapper
{

    /**
     * @var Injector
     */
    private $di;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->di = new Injector();
        $this->logger = $logger;
    }

    /**
     * @return $this
     * @throws \Auryn\ConfigException
     */
    public function prepare(): self
    {
        $this->prepareLogger();
        $this->prepareClientFactory();
        $this->prepareCache();
        $this->prepareSecretStore();
        $this->prepareGeocoder();
        return $this;
    }

    /**
     * @param Application $application
     * @return Application
     * @throws \Auryn\InjectionException
     */
    public function app(Application $application): Application
    {
        $commands = [
            SetSecretCommand::class,
            GetForecastCommand::class,
            GetHistoryCommand::class,
        ];
        foreach ($commands as $command) {
            $application->add($this->di->make($command));
        }

        return $application;
    }

    /**
     * @throws \Auryn\ConfigException
     * @throws \Auryn\InjectionException
     */
    public function prepareLogger(): void
    {
        $this->di
            ->share($this->logger)
            ->alias(LoggerInterface::class, \get_class($this->logger))
            ->prepare(
                LoggerAwareInterface::class,
                function (LoggerAwareInterface $needsLogger, Injector $di) {
                    $needsLogger->setLogger($di->make(LoggerInterface::class));
                }
            );
    }

    private function prepareClientFactory(): void
    {
        \philwc\DarkSky\ClientFactory::setLogger($this->logger);
    }

    /**
     * @throws \Auryn\ConfigException
     */
    private function prepareCache(): void
    {

        $this->di->define(Local::class, [
            ':root' => __DIR__ . '/../..'
        ]);

        $this->di->alias(AdapterInterface::class, Local::class);
        $this->di->alias(CacheInterface::class, FilesystemCachePool::class);
    }

    /**
     * @throws \Auryn\ConfigException
     */
    private function prepareGeocoder(): void
    {
        $this->di->define(Nominatim::class, [
            'client' => \Http\Adapter\Guzzle6\Client::class,
            ':rootUrl' => 'https://nominatim.openstreetmap.org/',
            ':userAgent' => 'philwc/dark-sky-cli',
        ]);

        $this->di->define(ProviderCache::class, [
            'realProvider' => Nominatim::class,
            'cache' => FilesystemCachePool::class,
            ':lifetime' => 86400
        ]);

        $this->di->define(StatefulGeocoder::class, [
            'provider' => ProviderCache::class,
            ':locale' => 'en'
        ]);

        $this->di->alias(Geocoder::class, StatefulGeocoder::class);
    }

    private function prepareSecretStore(): void
    {
        $this->di->define(SecretStore::class, [
            ':secretFile' => __DIR__ . '/../.darksky-secret'
        ]);
    }
}
