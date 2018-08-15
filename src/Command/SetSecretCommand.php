<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI\Command;

use philwc\DarkSky\CLI\SecretStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class SetSecretCommand
 * @package philwc\DarkSky\CLI\Command
 */
class SetSecretCommand extends Command
{
    /**
     * @var SecretStore
     */
    private $secretStore;

    /**
     * GenerateTokenCommand constructor.
     * @param SecretStore $secretStore
     */
    public function __construct(SecretStore $secretStore)
    {
        parent::__construct(null);
        $this->secretStore = $secretStore;
    }

    /**
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setName('auth')
            ->setDescription('Save your darksky secret to access the API');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $style->text(
            'Please visit `https://darksky.net/dev/account` to generate a token.' .
            ' Then paste it below' . PHP_EOL
        );

        $this->secretStore->set(
            $style->ask('Please enter your secret: ')
        );

        $style->success('Done!');
    }
}
