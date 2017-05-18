<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use League\Flysystem\Memory\MemoryAdapter;
use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Adaptor\MemoryAdaptorFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\MemoryAdaptorFactory
 */
class MemoryAdaptorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $factory = new MemoryAdaptorFactory();
        $class = $factory([]);

        $this->assertInstanceOf(MemoryAdapter::class, $class);
    }
}
