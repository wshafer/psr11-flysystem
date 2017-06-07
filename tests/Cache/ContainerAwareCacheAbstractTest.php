<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Cache;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Cache\ContainerAwareCacheAbstract;

/**
 * @covers \WShafer\PSR11FlySystem\Cache\ContainerAwareCacheAbstract
 */
class ContainerAwareCacheAbstractTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $containerMock;

    /** @var ContainerAwareCacheAbstract */
    protected $factory;

    public function setup()
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->factory = $this->getMockForAbstractClass(ContainerAwareCacheAbstract::class);
        $this->factory->setContainer($this->containerMock);
        $this->assertInstanceOf(ContainerAwareCacheAbstract::class, $this->factory);
        $this->assertEquals($this->containerMock, $this->factory->getContainer());
    }

    public function testGetAndSetContainer()
    {
    }

    /**
     * @expectedException \WShafer\PSR11FlySystem\Exception\MissingServiceException
     */
    public function testGetServiceNotFound()
    {
        $this->containerMock->expects($this->once())
            ->method('has')
            ->with('notHere')
            ->willReturn(false);

        $this->factory->getService('notHere');
    }

    public function testGetService()
    {
        $mockService = new \stdClass();

        $this->containerMock->expects($this->once())
            ->method('has')
            ->with('myService')
            ->willReturn(true);

        $this->containerMock->expects($this->once())
            ->method('get')
            ->with('myService')
            ->willReturn($mockService);

        $result = $this->factory->getService('myService');

        $this->assertEquals($mockService, $result);
    }
}
