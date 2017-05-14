<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Cache;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Cache\CacheMapper;
use WShafer\PSR11FlySystem\Cache\CacheMapperFactory;
use WShafer\PSR11FlySystem\Cache\MemoryCacheFactory;
use WShafer\PSR11FlySystem\Cache\Psr6CacheFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Cache\CacheMapperFactory
 */
class CacheMapperFactoryTest extends TestCase
{
    public function testGetFactoryClassNamePSR6()
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new CacheMapperFactory();
        $class = $factory($container);
        $this->assertInstanceOf(CacheMapper::class, $class);
    }
}
