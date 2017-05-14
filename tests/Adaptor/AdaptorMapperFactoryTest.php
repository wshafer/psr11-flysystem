<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Adaptor\AdaptorMapper;
use WShafer\PSR11FlySystem\Adaptor\AdaptorMapperFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\AdaptorMapperFactory
 */
class AdaptorMapperFactoryTest extends TestCase
{
    public function testGetFactoryClassNamePSR6()
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new AdaptorMapperFactory();
        $class = $factory($container);
        $this->assertInstanceOf(AdaptorMapper::class, $class);
    }
}
