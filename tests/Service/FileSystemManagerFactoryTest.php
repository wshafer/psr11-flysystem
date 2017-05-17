<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Service;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Cache\CacheMapper;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\MapperInterface;
use WShafer\PSR11FlySystem\Service\AdaptorManager;
use WShafer\PSR11FlySystem\Service\CacheManager;
use WShafer\PSR11FlySystem\Service\CacheManagerFactory;
use WShafer\PSR11FlySystem\Service\FileSystemManager;
use WShafer\PSR11FlySystem\Service\FileSystemManagerFactory;

class FileSystemManagerFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $config = $this->getMockBuilder(MainConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container = $this->createMock(ContainerInterface::class);

        $map = [
            [MainConfig::class, $config],
            [AdaptorManager::class, $container],
            [CacheManager::class, $container]
        ];

        $container->expects($this->exactly(3))
            ->method('get')
            ->will($this->returnValueMap($map));

        $factory = new FileSystemManagerFactory();
        $manager = $factory($container);

        $this->assertInstanceOf(FileSystemManager::class, $manager);
    }
}
