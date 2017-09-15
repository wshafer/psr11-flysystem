<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Cache;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Cached\Storage\Adapter;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Cache\AdaptorCacheFactory;
use WShafer\PSR11FlySystem\Cache\Psr6CacheFactory;
use WShafer\PSR11FlySystem\FlySystemFactory;
use WShafer\PSR11FlySystem\FlySystemManager;
use WShafer\PSR11FlySystem\Service\AdaptorManager;

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
        $mockAdaptorManager = $this->getMockBuilder(AdaptorManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockService = $this->getMockBuilder(FlySystemManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockService->expects($this->once())
            ->method('getAdaptorManager')
            ->willReturn($mockAdaptorManager);

        FlySystemFactory::setFlySystemManager($mockService);

        $mockFileAdaptor = $this->getMockBuilder(Local::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockAdaptorManager->expects($this->once())
            ->method('get')
            ->with('myAdaptor')
            ->willReturn($mockFileAdaptor);

        $mockAdaptorManager->expects($this->once())
            ->method('has')
            ->with('myAdaptor')
            ->willReturn(true);

        $options = [
            'adaptor' => 'myAdaptor',
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
            'adaptor' => null,
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
        $mockAdaptorManager = $this->getMockBuilder(AdaptorManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockService = $this->getMockBuilder(FlySystemManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockService->expects($this->once())
            ->method('getAdaptorManager')
            ->willReturn($mockAdaptorManager);

        FlySystemFactory::setFlySystemManager($mockService);

        $mockAdaptorManager->expects($this->never())
            ->method('get');

        $mockAdaptorManager->expects($this->once())
            ->method('has')
            ->with('myAdaptor')
            ->willReturn(false);

        $options = [
            'adaptor' => 'myAdaptor',
            'fileName' => 'cache_file',
            'ttl' => 300
        ];

        /** @var Adapter $cache */
        $cache = call_user_func($this->factory, $options);
    }
}
