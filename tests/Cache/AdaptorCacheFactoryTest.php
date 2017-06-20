<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Cache;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Cached\Storage\Adapter;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Cache\AdaptorCacheFactory;
use WShafer\PSR11FlySystem\Cache\Psr6CacheFactory;
use WShafer\PSR11FlySystem\FlySystemManager;

/**
 * @covers \WShafer\PSR11FlySystem\Cache\AdaptorCacheFactory
 */
class AdaptorCacheFactoryTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $containerMock;

    /** @var Psr6CacheFactory */
    protected $factory;

    public function setup()
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->factory = new AdaptorCacheFactory();
        $this->factory->setContainer($this->containerMock);
        $this->assertInstanceOf(AdaptorCacheFactory::class, $this->factory);
        $this->assertEquals($this->containerMock, $this->factory->getContainer());
    }

    public function testInvoke()
    {
        $mockService = $this->getMockBuilder(FlySystemManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockFileAdaptor = $this->getMockBuilder(Local::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockService->expects($this->once())
            ->method('get')
            ->with('mockFileAdaptor')
            ->willReturn($mockFileAdaptor);

        $mockService->expects($this->once())
            ->method('has')
            ->with('mockFileAdaptor')
            ->willReturn(true);

        $this->containerMock->expects($this->once())
            ->method('get')
            ->with(FlySystemManager::class)
            ->willReturn($mockService);

        $this->containerMock->expects($this->once())
            ->method('has')
            ->with(FlySystemManager::class)
            ->willReturn(true);

        $options = [
            'fileSystem' => 'mockFileAdaptor',
            'fileName' => 'cache_file',
            'ttl' => 300
        ];

        /** @var Adapter $cache */
        $cache = call_user_func($this->factory, $options);
        $this->assertInstanceOf(Adapter::class, $cache);
    }

    /**
     * @expectedException \WShafer\PSR11FlySystem\Exception\MissingConfigException
     */
    public function testInvokeServiceNotFound()
    {
        $options = [
            'fileSystem' => null,
            'fileName' => 'cache_file',
            'ttl' => 300
        ];

        call_user_func($this->factory, $options);
    }

    /**
     * @expectedException \WShafer\PSR11FlySystem\Exception\MissingServiceException
     */
    public function testInvokeFileSystemNotFound()
    {
        $mockService = $this->getMockBuilder(FlySystemManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockService->expects($this->once())
            ->method('has')
            ->with('mockFileAdaptor')
            ->willReturn(false);

        $mockService->expects($this->never())
            ->method('get')
            ->with('mockFileAdaptor');

        $this->containerMock->expects($this->once())
            ->method('get')
            ->with(FlySystemManager::class)
            ->willReturn($mockService);

        $this->containerMock->expects($this->once())
            ->method('has')
            ->with(FlySystemManager::class)
            ->willReturn(true);

        $options = [
            'fileSystem' => 'mockFileAdaptor',
            'fileName' => 'cache_file',
            'ttl' => 300
        ];

        /** @var Adapter $cache */
        $cache = call_user_func($this->factory, $options);
    }

    public function testInvokeWithCustomServiceName()
    {
        $mockService = $this->getMockBuilder(FlySystemManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockFileAdaptor = $this->getMockBuilder(Local::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockService->expects($this->once())
            ->method('get')
            ->with('mockFileAdaptor')
            ->willReturn($mockFileAdaptor);

        $mockService->expects($this->once())
            ->method('has')
            ->with('mockFileAdaptor')
            ->willReturn(true);

        $this->containerMock->expects($this->once())
            ->method('get')
            ->with('MyRenamedService')
            ->willReturn($mockService);

        $this->containerMock->expects($this->once())
            ->method('has')
            ->with('MyRenamedService')
            ->willReturn(true);

        $options = [
            'flyManagerServiceName' => 'MyRenamedService',
            'fileSystem' => 'mockFileAdaptor',
            'fileName' => 'cache_file',
            'ttl' => 300
        ];

        /** @var Adapter $cache */
        $cache = call_user_func($this->factory, $options);
        $this->assertInstanceOf(Adapter::class, $cache);
    }

    /**
     * @expectedException \WShafer\PSR11FlySystem\Exception\MissingServiceException
     */
    public function testCantFindCustomServiceName()
    {
        $this->containerMock->expects($this->never())
            ->method('get')
            ->with('notHere');

        $this->containerMock->expects($this->once())
            ->method('has')
            ->with('notHere')
            ->willReturn(false);

        $options = [
            'flyManagerServiceName' => 'notHere',
            'fileSystem' => 'mockFileAdaptor',
            'fileName' => 'cache_file',
            'ttl' => 300
        ];

        /** @var Adapter $cache */
        $cache = call_user_func($this->factory, $options);
    }
}
