<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI;

/**
 * Class SecretStore
 * @package philwc\DarkSky\CLI
 */
class SecretStore
{
    /**
     * @var string
     */
    private $secretFile;

    /**
     * SecretStore constructor.
     * @param string $secretFile
     */
    public function __construct(string $secretFile)
    {
        $this->secretFile = $secretFile;
    }

    /**
     * @param string $secret
     */
    public function set(string $secret): void
    {
        file_put_contents($this->secretFile, $secret);
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return file_get_contents($this->secretFile);
    }
}
