<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Adaptor;

use Blazon\PSR11FlySystem\Adapter\MemoryAdapterFactory;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blazon\PSR11FlySystem\Adapter\MemoryAdapterFactory
 */
class MemoryAdapterFactoryTest extends TestCase
{
    /** @var MemoryAdapterFactory */
    protected $factory;

    protected function setUp(): void
    {
        $this->factory = new MemoryAdapterFactory();
        $this->assertInstanceOf(MemoryAdapterFactory::class, $this->factory);
    }

    public function testConstructor()
    {
    }

    public function testInvoke()
    {
        $result = ($this->factory)([]);
        $this->assertInstanceOf(InMemoryFilesystemAdapter::class, $result);
    }
}
