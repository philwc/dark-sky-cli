<?php
declare(strict_types=1);

namespace philwc\DarkSky\CLI;

use PHPUnit\Framework\TestCase;

/**
 * Class SecretStoreTest
 * @package philwc\DarkSky\CLI
 * @covers \philwc\DarkSky\CLI\SecretStore
 */
class SecretStoreTest extends TestCase
{
    public function testGetSet()
    {
        $secretFile = tempnam(sys_get_temp_dir(), 'secret');
        $instance = new SecretStore($secretFile);
        $instance->set('secret');

        $this->assertEquals('secret', $instance->get());
    }
}
