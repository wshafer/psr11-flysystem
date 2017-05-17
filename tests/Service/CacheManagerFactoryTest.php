<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Service;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Cache\CacheMapper;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\MapperInterface;
use WShafer\PSR11FlySystem\Service\CacheManager;
use WShafer\PSR11FlySystem\Service\CacheManagerFactory;

class CacheManagerFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $config = $this->getMockBuilder(MainConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mapper = $this->createMock(MapperInterface::class);
        $container = $this->createMock(ContainerInterface::class);

        $map = [
            [MainConfig::class, $config],
            [CacheMapper::class, $mapper]
        ];

        $container->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap($map));

        $factory = new CacheManagerFactory();
        $manager = $factory($container);

        $this->assertInstanceOf(CacheManager::class, $manager);
    }
}
