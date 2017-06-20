<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Cache;

use League\Flysystem\Cached\Storage\Memcached;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Cache\MemcachedCacheFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Cache\MemcachedCacheFactory
 */
class MemcachedCacheFactoryTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $containerMock;

    /** @var MemcachedCacheFactory */
    protected $factory;

    public function setup()
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->factory = new MemcachedCacheFactory();
        $this->factory->setContainer($this->containerMock);
        $this->assertInstanceOf(MemcachedCacheFactory::class, $this->factory);
        $this->assertEquals($this->containerMock, $this->factory->getContainer());
    }

    public function testInvoke()
    {
        $mockService = $this->createMock(\Memcached::class);

        $this->containerMock->expects($this->once())
            ->method('get')
            ->with('mockService')
            ->willReturn($mockService);

        $this->containerMock->expects($this->once())
            ->method('has')
            ->with('mockService')
            ->willReturn(true);

        $options = [
            'service' => 'mockService',
            'key' => 'someKey',
            'ttl' => 300
        ];

        /** @var Memcached $cache */
        $cache = call_user_func($this->factory, $options);
        $this->assertInstanceOf(Memcached::class, $cache);
    }

    /**
     * @expectedException \WShafer\PSR11FlySystem\Exception\MissingServiceException
     */
    public function testInvokeServiceNotFound()
    {
        $this->containerMock->expects($this->once())
            ->method('has')
            ->with('notHere')
            ->willReturn(false);

        $options = [
            'service' => 'notHere',
            'key' => 'someKey',
            'ttl' => 300
        ];

        call_user_func($this->factory, $options);
    }
}
