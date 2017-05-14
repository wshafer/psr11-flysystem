<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Cache;

use League\Flysystem\Cached\Storage\Memory;
use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Cache\MemoryCacheFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Cache\MemoryCacheFactory
 */
class MemoryCacheFactoryTest extends TestCase
{
    public function testGetFactoryClassNamePSR6()
    {
        $factory = new MemoryCacheFactory();
        $class = $factory([]);
        $this->assertInstanceOf(Memory::class, $class);
    }
}
