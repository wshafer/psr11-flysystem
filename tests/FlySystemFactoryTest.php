<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test;

use Blazon\PSR11FlySystem\Adapter\AdapterMapper;
use Blazon\PSR11FlySystem\Config\Config;
use Blazon\PSR11FlySystem\Exception\InvalidConfigException;
use Blazon\PSR11FlySystem\Exception\InvalidContainerException;
use Blazon\PSR11FlySystem\Exception\MissingConfigException;
use Blazon\PSR11FlySystem\FlySystemFactory;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container as SymfonyContainer;

/** @covers \Blazon\PSR11FlySystem\FlySystemFactory */
class FlySystemFactoryTest extends TestCase
{
    public function testGetConfigArraySymfony()
    {
        $expected = [
            'flysystem' => [
                'some-key' => 'some-value',
                'some-other-key' => 'some-other-value',
            ]
        ];

        $mockContainer = $this->getMockBuilder(SymfonyContainer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockContainer->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('flysystem'))
            ->willReturn(true);

        $mockContainer->expects($this->once())
            ->method('getParameter')
            ->with($this->equalTo('flysystem'))
            ->willReturn($expected['flysystem']);

        $factory = new FlySystemFactory();
        $result = $factory->getConfigArray($mockContainer);

        $this->assertEquals($expected, $result);
    }

    public function testGetConfigArrayZend()
    {
        $expected = [
            'flysystem' => [
                'some-key' => 'some-value',
                'some-other-key' => 'some-other-value',
            ]
        ];

        $mockContainer = $this->createMock(ContainerInterface::class);

        $hasMap = [
            ['config', true],
            ['settings', false],
        ];

        $mockContainer->expects($this->atLeastOnce())
            ->method('has')
            ->willReturnMap($hasMap);

        $mockContainer->expects($this->once())
            ->method('get')
            ->with($this->equalTo('config'))
            ->willReturn($expected);

        $factory = new FlySystemFactory();
        $result = $factory->getConfigArray($mockContainer);

        $this->assertEquals($expected, $result);
    }

    public function testGetConfigArraySlim()
    {
        $expected = [
            'flysystem' => [
                'some-key' => 'some-value',
                'some-other-key' => 'some-other-value',
            ]
        ];

        $mockContainer = $this->createMock(ContainerInterface::class);

        $hasMap = [
            ['config', false],
            ['settings', true],
        ];

        $mockContainer->expects($this->atLeastOnce())
            ->method('has')
            ->willReturnMap($hasMap);

        $mockContainer->expects($this->once())
            ->method('get')
            ->with($this->equalTo('settings'))
            ->willReturn($expected);

        $factory = new FlySystemFactory();
        $result = $factory->getConfigArray($mockContainer);

        $this->assertEquals($expected, $result);
    }

    public function testGetConfigArrayMissing()
    {
        $this->expectException(MissingConfigException::class);
        $expected = [
            'flysystem' => [
                'some-key' => 'some-value',
                'some-other-key' => 'some-other-value',
            ]
        ];

        $mockContainer = $this->createMock(ContainerInterface::class);

        $hasMap = [
            ['config', false],
            ['settings', false],
        ];

        $mockContainer->expects($this->atLeastOnce())
            ->method('has')
            ->willReturnMap($hasMap);

        $mockContainer->expects($this->never())
            ->method('get');

        $factory = new FlySystemFactory();
        $factory->getConfigArray($mockContainer);
    }

    public function testGetConfig()
    {
        $expected = [
            'flysystem' => [
                'default' => [
                    'type' => 'memory',
                    'options' => []
                ],
            ],
        ];

        $mockContainer = $this->createMock(ContainerInterface::class);

        $hasMap = [
            ['config', true],
            ['settings', false],
        ];

        $mockContainer->expects($this->atLeastOnce())
            ->method('has')
            ->willReturnMap($hasMap);

        $mockContainer->expects($this->once())
            ->method('get')
            ->with($this->equalTo('config'))
            ->willReturn($expected);

        $factory = new FlySystemFactory();
        $result = $factory->getConfig($mockContainer);

        $this->assertInstanceOf(Config::class, $result);
    }

    public function testGetConfigWithServiceName()
    {
        $serviceKey = 'my-service-name';
        $expected = [
            'flysystem' => [
                $serviceKey => [
                    'type' => 'memory',
                    'options' => []
                ],
            ],
        ];

        $mockContainer = $this->createMock(ContainerInterface::class);

        $hasMap = [
            ['config', true],
            ['settings', false],
        ];

        $mockContainer->expects($this->atLeastOnce())
            ->method('has')
            ->willReturnMap($hasMap);

        $mockContainer->expects($this->once())
            ->method('get')
            ->with($this->equalTo('config'))
            ->willReturn($expected);

        $factory = new FlySystemFactory($serviceKey);
        $result = $factory->getConfig($mockContainer);

        $this->assertInstanceOf(Config::class, $result);
    }

    public function testGetConfigWithServiceNameMissingConfig()
    {
        $this->expectException(InvalidConfigException::class);
        $serviceKey = 'my-service-name';
        $expected = [
            'flysystem' => [
                'default' => [
                    'type' => 'memory',
                    'options' => []
                ],
            ],
        ];

        $mockContainer = $this->createMock(ContainerInterface::class);

        $hasMap = [
            ['config', true],
            ['settings', false],
        ];

        $mockContainer->expects($this->atLeastOnce())
            ->method('has')
            ->willReturnMap($hasMap);

        $mockContainer->expects($this->once())
            ->method('get')
            ->with($this->equalTo('config'))
            ->willReturn($expected);

        $factory = new FlySystemFactory($serviceKey);
        $factory->getConfig($mockContainer);
    }

    public function testGetMapper()
    {
        $mockContainer = $this->createMock(ContainerInterface::class);
        $factory = new FlySystemFactory();
        $result = $factory->getMapper($mockContainer);
        $this->assertInstanceOf(AdapterMapper::class, $result);
    }

    public function testInvoke()
    {
        $type = 'memory';
        $options = ['some-key' => 'some-value'];

        $factory = $this->getMockBuilder(FlySystemFactory::class)
            ->onlyMethods(['getConfig', 'getMapper'])
            ->getMock();

        $mockContainer = $this->createMock(ContainerInterface::class);

        $mockConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockMapper = $this->getMockBuilder(AdapterMapper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockAdapter = $this->getMockBuilder(FilesystemAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())
            ->method('getConfig')
            ->willReturn($mockConfig);

        $factory->expects($this->once())
            ->method('getMapper')
            ->willReturn($mockMapper);

        $mockConfig->expects($this->once())
            ->method('getType')
            ->willReturn($type);

        $mockConfig->expects($this->once())
            ->method('getOptions')
            ->willReturn($options);

        $mockMapper->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($type),
                $this->equalTo($options)
            )->willReturn($mockAdapter);

        $result = $factory($mockContainer);

        $this->assertInstanceOf(Filesystem::class, $result);
    }

    public function testCallStatic()
    {
        $service = 'someFilesystem';
        $type = 'some-service';

        $config = [
            'flysystem' => [
                $service => [
                    'type' => $type,
                    'options' => []
                ],
            ],
        ];

        $mockContainer = $this->createMock(ContainerInterface::class);

        $mockAdapter = $this->getMockBuilder(FilesystemAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $hasMap = [
            ['config', true],
            [$type, true]
        ];

        $mockContainer->expects($this->any())
            ->method('has')
            ->willReturnMap($hasMap);

        $getMap = [
            ['config', $config],
            [$type, $mockAdapter]
        ];

        $mockContainer->expects($this->any())
            ->method('get')
            ->willReturnMap($getMap);

        $result = FlySystemFactory::someFilesystem($mockContainer);
        $this->assertInstanceOf(Filesystem::class, $result);
    }

    public function testCallStaticMissingContainer()
    {
        $this->expectException(InvalidContainerException::class);
        FlySystemFactory::someFilesystem();
    }
}
