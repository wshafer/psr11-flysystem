<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test;

use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    public function testInvoke()
    {
        $provider = new ConfigProvider();

        $config = $provider();
        $this->assertArrayHasKey('dependencies', $config);
        $this->assertArrayHasKey('factories', $config['dependencies']);
    }
}
