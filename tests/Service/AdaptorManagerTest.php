<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Service;

use League\Flysystem\AdapterInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Config\AdaptorConfig;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\Exception\UnknownAdaptorException;
use WShafer\PSR11FlySystem\MapperInterface;
use WShafer\PSR11FlySystem\Service\AdaptorManager;

class AdaptorManagerTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|MainConfig */
    protected $mockConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject|AdaptorConfig */
    protected $mockAdaptorConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject|MapperInterface */
    protected $mockMapper;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    /** @var \PHPUnit_Framework_MockObject_MockObject|AdapterInterface */
    protected $mockAdaptor;

    /** @var  AdaptorManager */
    protected $manager;

    public function setup()
    {
        $this->mockConfig = $this->getMockBuilder(MainConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockAdaptorConfig = $this->getMockBuilder(AdaptorConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockMapper = $this->createMock(MapperInterface::class);
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockAdaptor = $this->createMock(AdapterInterface::class);

        $this->manager = new AdaptorManager(
            $this->mockConfig,
            $this->mockMapper,
            $this->mockContainer
        );
        $this->assertInstanceOf(AdaptorManager::class, $this->manager);
    }

    public function testConstructor()
    {
    }

    public function testHasTrue()
    {
        $this->mockConfig->expects($this->once())
            ->method('hasAdaptorConfig')
            ->with('IDo')
            ->willReturn(true);

        $this->assertTrue($this->manager->has('IDo'));
    }

    public function testHasFalse()
    {
        $this->mockConfig->expects($this->once())
            ->method('hasAdaptorConfig')
            ->with('IDo')
            ->willReturn(false);

        $this->assertFalse($this->manager->has('IDo'));
    }

    public function testGet()
    {
        $name = 'IHaveOne';
        $type = 'local';
        $options = [];

        $this->mockConfig->expects($this->once())
            ->method('hasAdaptorConfig')
            ->with($name)
            ->willReturn(true);

        $this->mockConfig->expects($this->once())
            ->method('getAdaptorConfig')
            ->with($name)
            ->willReturn($this->mockAdaptorConfig);

        $this->mockAdaptorConfig->expects($this->once())
            ->method('getType')
            ->willReturn($type);

        $this->mockAdaptorConfig->expects($this->once())
            ->method('getOptions')
            ->willReturn($options);

        $this->mockMapper->expects($this->once())
            ->method('get')
            ->with($type, $options)
            ->willReturn($this->mockAdaptor);

        $adaptor = $this->manager->get($name);

        $this->assertEquals($this->mockAdaptor, $adaptor);
    }

    public function testGetReturnsExistingAdaptor()
    {
        $name = 'IHaveOne';
        $type = 'local';
        $options = [];

        // Has will be called twice
        $this->mockConfig->expects($this->exactly(2))
            ->method('hasAdaptorConfig')
            ->with($name)
            ->willReturn(true);

        // Only called on the first request
        $this->mockConfig->expects($this->once())
            ->method('getAdaptorConfig')
            ->with($name)
            ->willReturn($this->mockAdaptorConfig);

        // Only called on the first request
        $this->mockAdaptorConfig->expects($this->once())
            ->method('getType')
            ->willReturn($type);

        // Only called on the first request
        $this->mockAdaptorConfig->expects($this->once())
            ->method('getOptions')
            ->willReturn($options);

        // Only called on the first request
        $this->mockMapper->expects($this->once())
            ->method('get')
            ->with($type, $options)
            ->willReturn($this->mockAdaptor);

        // Request One
        $this->manager->get($name);

        // This should be from the previously build adaptor
        $adaptor = $this->manager->get($name);

        $this->assertEquals($this->mockAdaptor, $adaptor);
    }

    public function testGetNotFoundException()
    {
        $this->expectException(UnknownAdaptorException::class);
        $this->mockConfig->expects($this->once())
            ->method('hasAdaptorConfig')
            ->with('IDo')
            ->willReturn(false);

        $this->manager->get('IDo');
    }
}
