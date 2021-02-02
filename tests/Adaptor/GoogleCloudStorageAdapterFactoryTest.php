<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Adaptor;

use Blazon\PSR11FlySystem\Adapter\GoogleCloudStorageAdapterFactory;
use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\GoogleCloudStorage\PortableVisibilityHandler;
use League\Flysystem\PathPrefixer;
use League\Flysystem\Visibility;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionProperty;

/**
 * @covers \Blazon\PSR11FlySystem\Adapter\GoogleCloudStorageAdapterFactory
 */
class GoogleCloudStorageAdapterFactoryTest extends TestCase
{
    /** @var GoogleCloudStorageAdapterFactory */
    protected $factory;

    /** @var ContainerInterface|MockObject */
    protected $mockContainer;

    protected function setUp(): void
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);

        $this->factory = new GoogleCloudStorageAdapterFactory();
        $this->assertInstanceOf(GoogleCloudStorageAdapterFactory::class, $this->factory);

        $this->factory->setContainer($this->mockContainer);
    }

    public function testConstructor()
    {
    }

    public function testGetVisibilityHandler()
    {
        $entity = 'some-entity';
        $publicAcl = PortableVisibilityHandler::ACL_PRIVATE;
        $privateAcl = PortableVisibilityHandler::ACL_PUBLIC_READ;

        $result = $this->factory->getVisibilityHandler([
            'entity' => $entity,
            'publicAcl' => $publicAcl,
            'privateAcl' => $privateAcl
        ]);

        $this->assertInstanceOf(PortableVisibilityHandler::class, $result);

        $entityCheck = new ReflectionProperty(PortableVisibilityHandler::class, 'entity');
        $entityCheck->setAccessible(true);
        $this->assertEquals($entity, $entityCheck->getValue($result));

        $publicAclCheck = new ReflectionProperty(
            PortableVisibilityHandler::class,
            'predefinedPublicAcl'
        );

        $publicAclCheck->setAccessible(true);
        $this->assertEquals($publicAcl, $publicAclCheck->getValue($result));

        $privateAclCheck = new ReflectionProperty(
            PortableVisibilityHandler::class,
            'predefinedPrivateAcl'
        );

        $privateAclCheck->setAccessible(true);
        $this->assertEquals($privateAcl, $privateAclCheck->getValue($result));
    }

    public function testGetVisibilityHandlerWithDefaults()
    {
        $entity = 'allUsers';
        $publicAcl = PortableVisibilityHandler::ACL_PUBLIC_READ;
        $privateAcl = PortableVisibilityHandler::ACL_PROJECT_PRIVATE;

        $result = $this->factory->getVisibilityHandler([]);

        $this->assertInstanceOf(PortableVisibilityHandler::class, $result);

        $entityCheck = new ReflectionProperty(PortableVisibilityHandler::class, 'entity');
        $entityCheck->setAccessible(true);
        $this->assertEquals($entity, $entityCheck->getValue($result));

        $publicAclCheck = new ReflectionProperty(
            PortableVisibilityHandler::class,
            'predefinedPublicAcl'
        );

        $publicAclCheck->setAccessible(true);
        $this->assertEquals($publicAcl, $publicAclCheck->getValue($result));

        $privateAclCheck = new ReflectionProperty(
            PortableVisibilityHandler::class,
            'predefinedPrivateAcl'
        );

        $privateAclCheck->setAccessible(true);
        $this->assertEquals($privateAcl, $privateAclCheck->getValue($result));
    }

    public function testGetClient()
    {
        $client = $this->factory->getClient([]);
        $this->assertInstanceOf(StorageClient::class, $client);
    }

    public function testGetClientService()
    {
        $serviceName = 'myService';

        $mockService = $this->getMockBuilder(StorageClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with($this->equalTo($serviceName))
            ->willReturn($mockService);

        $client = $this->factory->getClient([], $serviceName);

        $this->assertEquals($mockService, $client);
    }

    public function testGetBucket()
    {
        $client = 'myService';
        $bucketName = 'someName';
        $clientOptions = [];

        $mockService = $this->getMockBuilder(StorageClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockBucket = $this->getMockBuilder(Bucket::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with($this->equalTo($bucketName))
            ->willReturn(false);

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with($this->equalTo($client))
            ->willReturn($mockService);

        $mockService->expects($this->once())
            ->method('bucket')
            ->with($this->equalTo($bucketName))
            ->willReturn($mockBucket);

        $result = $this->factory->getBucket($bucketName, $clientOptions, $client);

        $this->assertEquals($mockBucket, $result);
    }

    public function testGetBucketService()
    {
        $client = 'myService';
        $bucketName = 'someName';
        $clientOptions = [];

        $mockBucket = $this->getMockBuilder(Bucket::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with($this->equalTo($bucketName))
            ->willReturn(true);

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with($this->equalTo($bucketName))
            ->willReturn($mockBucket);

        $result = $this->factory->getBucket($bucketName, $clientOptions, $client);

        $this->assertEquals($mockBucket, $result);
    }

    public function testInvoke()
    {
        $client = 'myService';
        $bucketName = 'someName';
        $clientOptions = ['some-key' => 'some-value'];
        $permissions = ['some-permission-key' => 'some-permission-value'];
        $prefix = 'my-prefix/';
        $defaultVisibility = Visibility::PRIVATE;

        /** @var GoogleCloudStorageAdapterFactory|MockObject $mockFactory */
        $mockFactory = $this->getMockBuilder(GoogleCloudStorageAdapterFactory::class)
            ->onlyMethods(['getBucket', 'getVisibilityHandler'])
            ->getMock();

        $mockBucket = $this->getMockBuilder(Bucket::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockVisibilityHandler = $this->getMockBuilder(PortableVisibilityHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockFactory->expects($this->once())
            ->method('getBucket')
            ->with(
                $this->equalTo($bucketName),
                $this->equalTo($clientOptions),
                $this->equalTo($client)
            )->willReturn($mockBucket);

        $mockFactory->expects($this->once())
            ->method('getVisibilityHandler')
            ->with($this->equalTo($permissions))
            ->willReturn($mockVisibilityHandler);

        $options = [
            'bucket' => $bucketName,
            'client' => $client,
            'prefix' => $prefix,
            'defaultVisibility' => $defaultVisibility,
            'permissions' => $permissions,
            'clientOptions' => $clientOptions
        ];

        $result = $mockFactory->__invoke($options);

        $this->assertInstanceOf(GoogleCloudStorageAdapter::class, $result);

        // Check bucket was set correctly
        $bucketCheck = new ReflectionProperty(
            GoogleCloudStorageAdapter::class,
            'bucket'
        );

        $bucketCheck->setAccessible(true);
        $this->assertEquals($mockBucket, $bucketCheck->getValue($result));

        // Get prefixer to validate prefix
        $prefixerCheck = new ReflectionProperty(
            GoogleCloudStorageAdapter::class,
            'prefixer'
        );

        $prefixerCheck->setAccessible(true);
        $prefixer = $prefixerCheck->getValue($result);
        $this->assertInstanceOf(PathPrefixer::class, $prefixer);

        // Check prefix was set correctly
        $prefixCheck = new ReflectionProperty(
            PathPrefixer::class,
            'prefix'
        );

        $prefixCheck->setAccessible(true);
        $this->assertEquals($prefix, $prefixCheck->getValue($prefixer));


        // Check visibilityHandler was set correctly
        $visibilityHandlerCheck = new ReflectionProperty(
            GoogleCloudStorageAdapter::class,
            'visibilityHandler'
        );

        $visibilityHandlerCheck->setAccessible(true);
        $this->assertEquals($mockVisibilityHandler, $visibilityHandlerCheck->getValue($result));

        // Check defaultVisibility was set correctly
        $defaultVisibilityCheck = new ReflectionProperty(
            GoogleCloudStorageAdapter::class,
            'defaultVisibility'
        );

        $defaultVisibilityCheck->setAccessible(true);
        $this->assertEquals($defaultVisibility, $defaultVisibilityCheck->getValue($result));
    }
}
