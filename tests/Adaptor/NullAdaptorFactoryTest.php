<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use League\Flysystem\Adapter\NullAdapter;
use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Adaptor\NullAdaptorFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\NullAdaptorFactory
 */
class NullAdaptorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $factory = new NullAdaptorFactory();
        $class = $factory([]);
        $this->assertInstanceOf(NullAdapter::class, $class);
    }
}
