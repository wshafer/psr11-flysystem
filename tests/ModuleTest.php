<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test;

use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Module;

class ModuleTest extends TestCase
{
    public function testGetConfig()
    {
        $module = new Module();

        $config = $module->getConfig();

        $this->assertTrue(is_array($config));
    }
}
