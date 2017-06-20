<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Cache;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Cache\AdaptorCacheFactory;
use WShafer\PSR11FlySystem\Cache\CacheMapper;
use WShafer\PSR11FlySystem\Cache\MemcachedCacheFactory;
use WShafer\PSR11FlySystem\Cache\MemoryCacheFactory;
use WShafer\PSR11FlySystem\Cache\PredisCacheFactory;
use WShafer\PSR11FlySystem\Cache\Psr6CacheFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Cache\CacheMapper
 */
class CacheMapperTest extends TestCase
{
    public function testGetFactoryClassNamePredis()
    {
        $container = $this->createMock(ContainerInterface::class);
        $cacheMapper = new CacheMapper($container);
        $result = $cacheMapper->getFactoryClassName('predis');
        $this->assertEquals(PredisCacheFactory::class, $result);
    }

    public function testGetFactoryClassNameMemcached()
    {
        $container = $this->createMock(ContainerInterface::class);
        $cacheMapper = new CacheMapper($container);
        $result = $cacheMapper->getFactoryClassName('memcached');
        $this->assertEquals(MemcachedCacheFactory::class, $result);
    }

    public function testGetFactoryClassNamePSR6()
    {
        $container = $this->createMock(ContainerInterface::class);
        $cacheMapper = new CacheMapper($container);
        $result = $cacheMapper->getFactoryClassName('psr6');
        $this->assertEquals(Psr6CacheFactory::class, $result);
    }

    public function testGetFactoryClassNameMemory()
    {
        $container = $this->createMock(ContainerInterface::class);
        $cacheMapper = new CacheMapper($container);
        $result = $cacheMapper->getFactoryClassName('memory');
        $this->assertEquals(MemoryCacheFactory::class, $result);
    }

    public function testGetFactoryClassNameAdaptor()
    {
        $container = $this->createMock(ContainerInterface::class);
        $cacheMapper = new CacheMapper($container);
        $result = $cacheMapper->getFactoryClassName('adaptor');
        $this->assertEquals(AdaptorCacheFactory::class, $result);
    }

    public function testGetFactoryClassNameAdapter()
    {
        $container = $this->createMock(ContainerInterface::class);
        $cacheMapper = new CacheMapper($container);
        $result = $cacheMapper->getFactoryClassName('adapter');
        $this->assertEquals(AdaptorCacheFactory::class, $result);
    }

    public function testGetFactoryClassNameByFullClass()
    {
        $container = $this->createMock(ContainerInterface::class);
        $cacheMapper = new CacheMapper($container);
        $result = $cacheMapper->getFactoryClassName(MemoryCacheFactory::class);
        $this->assertEquals(MemoryCacheFactory::class, $result);
    }

    public function testGetFactoryClassNameNotFound()
    {
        $container = $this->createMock(ContainerInterface::class);
        $cacheMapper = new CacheMapper($container);
        $result = $cacheMapper->getFactoryClassName('Oops-Not-Found');
        $this->assertNull($result);
    }

    /**
     * @expectedException \TypeError
     */
    public function testGetFactoryClassNameOnlyAcceptsStrings()
    {
        $container = $this->createMock(ContainerInterface::class);
        $cacheMapper = new CacheMapper($container);
        $cacheMapper->getFactoryClassName(123);
    }
}
