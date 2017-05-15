<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use League\Flysystem\Adapter\Local;
use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Adaptor\LocalAdaptorFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\LocalAdaptorFactory
 */
class LocalAdaptorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $factory = new LocalAdaptorFactory();
        $class = $factory([
            'root' => '/tmp'
        ]);
        $this->assertInstanceOf(Local::class, $class);
    }
}
