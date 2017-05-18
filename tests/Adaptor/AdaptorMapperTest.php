<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Adaptor\AdaptorMapper;
use WShafer\PSR11FlySystem\Adaptor\AzureAdapterFactory;
use WShafer\PSR11FlySystem\Adaptor\DropBoxAdapterFactory;
use WShafer\PSR11FlySystem\Adaptor\LocalAdaptorFactory;
use WShafer\PSR11FlySystem\Adaptor\NullAdaptorFactory;
use WShafer\PSR11FlySystem\Adaptor\S3AdapterFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\AdaptorMapper
 */
class AdaptorMapperTest extends TestCase
{
    public function testGetFactoryClassNameDropBox()
    {
        $container = $this->createMock(ContainerInterface::class);
        $adaptorMapper = new AdaptorMapper($container);
        $result = $adaptorMapper->getFactoryClassName('dropbox');
        $this->assertEquals(DropBoxAdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameS3()
    {
        $container = $this->createMock(ContainerInterface::class);
        $adaptorMapper = new AdaptorMapper($container);
        $result = $adaptorMapper->getFactoryClassName('s3');
        $this->assertEquals(S3AdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameAzure()
    {
        $container = $this->createMock(ContainerInterface::class);
        $adaptorMapper = new AdaptorMapper($container);
        $result = $adaptorMapper->getFactoryClassName('azure');
        $this->assertEquals(AzureAdapterFactory::class, $result);
    }

    public function testGetFactoryClassNameLocal()
    {
        $container = $this->createMock(ContainerInterface::class);
        $adaptorMapper = new AdaptorMapper($container);
        $result = $adaptorMapper->getFactoryClassName('local');
        $this->assertEquals(LocalAdaptorFactory::class, $result);
    }

    public function testGetFactoryClassNameNull()
    {
        $container = $this->createMock(ContainerInterface::class);
        $adaptorMapper = new AdaptorMapper($container);
        $result = $adaptorMapper->getFactoryClassName('null');
        $this->assertEquals(NullAdaptorFactory::class, $result);
    }

    public function testGetFactoryClassNameByFullClass()
    {
        $container = $this->createMock(ContainerInterface::class);
        $adaptorMapper = new AdaptorMapper($container);
        $result = $adaptorMapper->getFactoryClassName(NullAdaptorFactory::class);
        $this->assertEquals(NullAdaptorFactory::class, $result);
    }

    public function testGetFactoryClassNameNotFound()
    {
        $container = $this->createMock(ContainerInterface::class);
        $adaptorMapper = new AdaptorMapper($container);
        $result = $adaptorMapper->getFactoryClassName('Oops-Not-Found');
        $this->assertNull($result);
    }

    /**
     * @expectedException \TypeError
     */
    public function testGetFactoryClassNameOnlyAcceptsStrings()
    {
        $container = $this->createMock(ContainerInterface::class);
        $adaptorMapper = new AdaptorMapper($container);
        $adaptorMapper->getFactoryClassName(123);
    }
}
