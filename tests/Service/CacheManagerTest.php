<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Service;

use League\Flysystem\Cached\CacheInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Adaptor\MemoryAdaptorFactory;
use WShafer\PSR11FlySystem\Config\CacheConfig;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\Exception\UnknownCacheException;
use WShafer\PSR11FlySystem\MapperInterface;
use WShafer\PSR11FlySystem\Service\AdaptorManager;
use WShafer\PSR11FlySystem\Service\CacheManager;

class CacheManagerTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|MainConfig */
    protected $mockConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CacheConfig */
    protected $mockCacheConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject|MapperInterface */
    protected $mockMapper;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CacheInterface */
    protected $mockCache;

    /** @var  AdaptorManager */
    protected $manager;

    public function setup()
    {
        $this->mockConfig = $this->getMockBuilder(MainConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockCacheConfig = $this->getMockBuilder(CacheConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockMapper = $this->createMock(MapperInterface::class);
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockCache = $this->createMock(CacheInterface::class);

        $this->manager = new CacheManager(
            $this->mockConfig,
            $this->mockMapper,
            $this->mockContainer
        );
        $this->assertInstanceOf(CacheManager::class, $this->manager);
    }

    public function testConstructor()
    {
    }

    public function testHasTrue()
    {
        $this->mockConfig->expects($this->once())
            ->method('hasCacheConfig')
            ->with('IDo')
            ->willReturn(true);

        $this->assertTrue($this->manager->has('IDo'));
    }

    public function testHasFalse()
    {
        $this->mockConfig->expects($this->once())
            ->method('hasCacheConfig')
            ->with('IDo')
            ->willReturn(false);

        $this->assertFalse($this->manager->has('IDo'));
    }

    public function testGet()
    {
        $name = 'IHaveOne';
        $type = 'memory';
        $options = [];

        $this->mockConfig->expects($this->once())
            ->method('hasCacheConfig')
            ->with($name)
            ->willReturn(true);

        $this->mockConfig->expects($this->once())
            ->method('getCacheConfig')
            ->with($name)
            ->willReturn($this->mockCacheConfig);

        $this->mockCacheConfig->expects($this->once())
            ->method('getType')
            ->willReturn($type);

        $this->mockCacheConfig->expects($this->once())
            ->method('getOptions')
            ->willReturn($options);

        $this->mockMapper->expects($this->once())
            ->method('get')
            ->with($type, $options)
            ->willReturn($this->mockCache);

        $adaptor = $this->manager->get($name);

        $this->assertEquals($this->mockCache, $adaptor);
    }

    public function testGetDefault()
    {
        $name = 'default';

        $this->mockConfig->expects($this->never())
            ->method('hasCacheConfig')
            ->with($name);

        $this->mockConfig->expects($this->never())
            ->method('getCacheConfig')
            ->with($name);

        $this->mockCacheConfig->expects($this->never())
            ->method('getType');

        $this->mockCacheConfig->expects($this->never())
            ->method('getOptions');

        $this->mockMapper->expects($this->once())
            ->method('get')
            ->with('memory', [])
            ->willReturn($this->mockCache);

        $adaptor = $this->manager->get($name);

        $this->assertEquals($this->mockCache, $adaptor);
    }

    public function testGetNotFoundException()
    {
        $this->expectException(UnknownCacheException::class);
        $this->mockConfig->expects($this->once())
            ->method('hasCacheConfig')
            ->with('IDo')
            ->willReturn(false);

        $this->manager->get('IDo');
    }
}
