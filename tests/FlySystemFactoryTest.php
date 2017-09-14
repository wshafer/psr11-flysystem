<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test;

use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\FlySystemFactory;
use WShafer\PSR11FlySystem\FlySystemManager;

class FlySystemFactoryTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|FlySystemFactory */
    protected $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|FlySystemManager */
    protected $mockManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject|FilesystemInterface */
    protected $mockFileSystem;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    public function setup()
    {
        $this->mockManager = $this->getMockBuilder(FlySystemManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockFileSystem = $this->createMock(FilesystemInterface::class);

        FlySystemFactory::setFlySystemManager($this->mockManager);
        $this->assertEquals(FlySystemFactory::getFlySystemManager($this->mockContainer), $this->mockManager);

        $this->factory = new FlySystemFactory();

        $this->assertInstanceOf(FlySystemFactory::class, $this->factory);
    }

    public function testConstructor()
    {
    }

    public function testSetAndGetFileSystemName()
    {
        $expected = 'some-name';

        $this->factory->setFileSystemName($expected);

        $result = $this->factory->getFileSystemName();

        $this->assertEquals($expected, $result);
    }

    public function testInvokeWithDefault()
    {
        $this->mockManager->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($this->mockFileSystem);

        $return = $this->factory->__invoke($this->mockContainer);

        $this->assertEquals($this->mockFileSystem, $return);
    }

    public function testInvokeWithOtherName()
    {
        $this->mockManager->expects($this->once())
            ->method('get')
            ->with('other')
            ->willReturn($this->mockFileSystem);

        $this->factory->setFileSystemName('other');

        $return = $this->factory->__invoke($this->mockContainer);

        $this->assertEquals($this->mockFileSystem, $return);
    }

    public function testCallStatic()
    {
        $this->mockManager->expects($this->once())
            ->method('get')
            ->with('other')
            ->willReturn($this->mockFileSystem);

        $return = $this->factory::other($this->mockContainer);

        $this->assertEquals($this->mockFileSystem, $return);
    }

    /**
     * @expectedException \WShafer\PSR11FlySystem\Exception\InvalidContainerException
     */
    public function testCallStaticNoContainer()
    {
        $this->factory::other('not a container');
    }
}
