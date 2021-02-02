<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Adaptor;

use AsyncAws\S3\S3Client;
use Blazon\PSR11FlySystem\Adapter\AsyncAwsS3AdapterFactory;
use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Blazon\PSR11FlySystem\Adapter\AsyncAwsS3AdapterFactory
 */
class AsyncAwsS3AdapterFactoryTest extends TestCase
{
    /** @var AsyncAwsS3AdapterFactory */
    protected $factory;

    /** @var MockObject|ContainerInterface */
    protected $mockContainer;

    protected $mockClient;

    protected function setUp(): void
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockClient = $this->getMockBuilder(S3Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new AsyncAwsS3AdapterFactory();
        $this->factory->setContainer($this->mockContainer);

        $this->assertInstanceOf(AsyncAwsS3AdapterFactory::class, $this->factory);
    }

    public function testConstructor()
    {
    }

    public function testGetClient()
    {
        $options = [
            'key' => 'some-key',
            'secret' => 'abcdefg',
            'region' => 'us-east-1'
        ];

        $result = $this->factory->getClient($options);

        $this->assertInstanceOf(S3Client::class, $result);
    }

    public function testGetClientFromContainer()
    {
        $options = ['client' => 'some-client'];

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with($this->equalTo($options['client']))
            ->willReturn($this->mockClient);

        $result = $this->factory->getClient($options);

        $this->assertEquals($this->mockClient, $result);
    }

    public function testInvoke()
    {
        $options = ['client' => 'some-client'];

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with($this->equalTo($options['client']))
            ->willReturn($this->mockClient);

        $result = ($this->factory)($options);

        $this->assertInstanceOf(AsyncAwsS3Adapter::class, $result);
    }
}
