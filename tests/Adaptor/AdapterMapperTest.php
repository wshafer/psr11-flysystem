<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Adaptor;

use Blazon\PSR11FlySystem\Adapter\AdapterMapper;
use Blazon\PSR11FlySystem\Adapter\AsyncAwsS3AdapterFactory;
use Blazon\PSR11FlySystem\Adapter\FtpAdapterFactory;
use Blazon\PSR11FlySystem\Adapter\GoogleCloudStorageAdapterFactory;
use Blazon\PSR11FlySystem\Adapter\LocalAdapterFactory;
use Blazon\PSR11FlySystem\Adapter\MemoryAdapterFactory;
use Blazon\PSR11FlySystem\Adapter\S3AdapterFactory;
use Blazon\PSR11FlySystem\Adapter\SftpAdapterFactory;
use Blazon\PSR11FlySystem\Adapter\ZipArchiveAdapterFactory;
use Blazon\PSR11FlySystem\Exception\InvalidConfigException;
use Blazon\PSR11FlySystem\Test\Mocks\FactoryMock;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Blazon\PSR11FlySystem\Adapter\AdapterMapper
 */
class AdapterMapperTest extends TestCase
{
    /** @var AdapterMapper */
    protected $mapper;

    /** @var MockObject|ContainerInterface */
    protected $mockContainer;

    protected function setUp(): void
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mapper = new AdapterMapper($this->mockContainer);

        // Reset mock factory
        FactoryMock::$container = null;

        $this->assertInstanceOf(AdapterMapper::class, $this->mapper);
    }

    public function testConstructor()
    {
    }

    public function testGetFactoryClassNameNoClassExists()
    {
        $result = $this->mapper->getFactoryClassName('DoesNotExist');
        $this->assertNull($result);
    }

    public function testGetFactoryClassNameAsyncAwsS3()
    {
        $result = $this->mapper->getFactoryClassName('asyncawss3');
        $this->assertEquals(AsyncAwsS3AdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('AsyncAwsS3');
        $this->assertEquals(AsyncAwsS3AdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameS3()
    {
        $result = $this->mapper->getFactoryClassName('s3');
        $this->assertEquals(S3AdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('S3');
        $this->assertEquals(S3AdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameAwsS3V3()
    {
        $result = $this->mapper->getFactoryClassName('awss3v3');
        $this->assertEquals(S3AdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('AwsS3V3');
        $this->assertEquals(S3AdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameFtp()
    {
        $result = $this->mapper->getFactoryClassName('ftp');
        $this->assertEquals(FtpAdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('FTP');
        $this->assertEquals(FtpAdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('Ftp');
        $this->assertEquals(FtpAdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameGoogleCloudStorage()
    {
        $result = $this->mapper->getFactoryClassName('googlecloudstorage');
        $this->assertEquals(GoogleCloudStorageAdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('GoogleCloudStorage');
        $this->assertEquals(GoogleCloudStorageAdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameLocal()
    {
        $result = $this->mapper->getFactoryClassName('local');
        $this->assertEquals(LocalAdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('Local');
        $this->assertEquals(LocalAdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameMemory()
    {
        $result = $this->mapper->getFactoryClassName('memory');
        $this->assertEquals(MemoryAdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('Memory');
        $this->assertEquals(MemoryAdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameInMemory()
    {
        $result = $this->mapper->getFactoryClassName('inmemory');
        $this->assertEquals(MemoryAdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('InMemory');
        $this->assertEquals(MemoryAdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameSftp()
    {
        $result = $this->mapper->getFactoryClassName('sftp');
        $this->assertEquals(SftpAdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('SFTP');
        $this->assertEquals(SftpAdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameZip()
    {
        $result = $this->mapper->getFactoryClassName('zip');
        $this->assertEquals(ZipArchiveAdapterFactory::class, $result);

        $result = $this->mapper->getFactoryClassName('ZIP');
        $this->assertEquals(ZipArchiveAdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameFactoryMock()
    {
        $result = $this->mapper->getFactoryClassName(FactoryMock::class);
        $this->assertEquals(FactoryMock::class, $result);
    }

    public function testGet()
    {
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->willReturn(null);

        $this->mockContainer->expects($this->never())
            ->method('get');

        $result = $this->mapper->get(FactoryMock::class, []);

        $this->assertInstanceOf(InMemoryFilesystemAdapter::class, $result);
        $this->assertEquals($this->mockContainer, FactoryMock::$container);
    }

    public function testGetInvalidClass()
    {
        $this->expectException(InvalidConfigException::class);

        $this->mockContainer->expects($this->once())
            ->method('has')
            ->willReturn(null);

        $this->mockContainer->expects($this->never())
            ->method('get');

        $this->mapper->get(TestCase::class, []);
    }

    public function testGetWithUnknownClass()
    {
        $this->expectException(InvalidConfigException::class);

        $this->mockContainer->expects($this->once())
            ->method('has')
            ->willReturn(null);

        $this->mockContainer->expects($this->never())
            ->method('get');

        $this->mapper->get('DoesNotExist', []);
    }

    public function testGetWithContainerService()
    {
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->willReturn(true);

        $mockAdaptor = $this->createMock(FilesystemAdapter::class);

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->willReturn($mockAdaptor);

        $result = $this->mapper->get('MyService', []);

        $this->assertEquals($mockAdaptor, $result);
    }

    public function testHas()
    {
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->willReturn(false);

        $result = $this->mapper->has(TestCase::class);

        $this->assertTrue($result);
    }

    public function testHasClassNotFound()
    {
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->willReturn(false);

        $result = $this->mapper->has('DoesNotExist');

        $this->assertFalse($result);
    }

    public function testHasService()
    {
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->willReturn(true);

        $result = $this->mapper->has('MyService');

        $this->assertTrue($result);
    }
}
