<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Exception\InvalidConfigException;
use WShafer\PSR11FlySystem\MapperAbstract;
use WShafer\PSR11FlySystem\MapperInterface;
use WShafer\PSR11FlySystem\Test\Stub\FactoryStub;
use WShafer\PSR11FlySystem\Test\Stub\MapperStub;

class MapperAbstractTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    /** @var MapperInterface */
    protected $mapper;

    public function setup()
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mapper = new MapperStub($this->mockContainer);

        $this->assertInstanceOf(MapperAbstract::class, $this->mapper);
    }

    public function testConstructor()
    {
    }

    public function testGetHasService()
    {
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with('service')
            ->willReturn(true);

        $service = new \stdClass();
        $service->dummyData = true;

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('service')
            ->willReturn($service);

        $result = $this->mapper->get('service', []);
        $this->assertEquals($service, $result);
    }

    public function testGetHasFactoryClass()
    {
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with(FactoryStub::class)
            ->willReturn(false);

        $this->mockContainer->expects($this->never())
            ->method('get')
            ->with('service');

        $options = [
            'test' => true
        ];

        $result = $this->mapper->get(FactoryStub::class, $options);
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertTrue($result->test);
    }

    public function testGetMissingFactory()
    {
        $this->expectException(InvalidConfigException::class);
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with('doesNotExist')
            ->willReturn(false);

        $this->mockContainer->expects($this->never())
            ->method('get')
            ->with('service');

        $options = [
            'test' => true
        ];

        $this->mapper->get('doesNotExist', $options);
    }

    public function testGetWithInvalidFactory()
    {
        $this->expectException(InvalidConfigException::class);

        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with(\stdClass::class)
            ->willReturn(false);

        $this->mockContainer->expects($this->never())
            ->method('get')
            ->with('service');

        $options = [
            'test' => true
        ];

        $this->mapper->get(\stdClass::class, $options);
    }

    public function testHasWithService()
    {
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with('service')
            ->willReturn(true);

        $this->assertTrue($this->mapper->has('service'));
    }

    public function testHasWithFactoryClass()
    {
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with(\stdClass::class)
            ->willReturn(false);

        $this->assertTrue($this->mapper->has(\stdClass::class));
    }

    public function testHasNotFound()
    {
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with('notHere')
            ->willReturn(false);

        $this->assertFalse($this->mapper->has('notHere'));
    }
}
