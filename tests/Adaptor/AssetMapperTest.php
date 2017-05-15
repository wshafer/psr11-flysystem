<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Adaptor\AdaptorMapper;
use WShafer\PSR11FlySystem\Adaptor\LocalAdaptorFactory;
use WShafer\PSR11FlySystem\Adaptor\NullAdaptorFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\AdaptorMapper
 */
class AdaptorMapperTest extends TestCase
{
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
