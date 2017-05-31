<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Adaptor\AdaptorMapper;
use WShafer\PSR11FlySystem\Cache\CacheMapper;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\FlySystemManager;
use WShafer\PSR11FlySystem\FlySystemManagerFactory;
use WShafer\PSR11FlySystem\Service\AdaptorManager;
use WShafer\PSR11FlySystem\Service\CacheManager;

class FlySystemManagerFactoryTest extends TestCase
{
    /** @var FlySystemManagerFactory */
    protected $factory;

    /** @var ContainerInterface */
    protected $container;

    protected function getConfig()
    {
        return [
            'flysystem' => [
                'adaptors' => [
                    'adaptorOne' => [
                        'type' => 'null',
                        'options' => [],
                    ],

                    'adaptorTwo' => [
                        'type' => 'null',
                        'options' => [],
                    ],
                ],

                'caches' => [
                    'cacheOne' => [
                        'type' => 'memory',
                        'options' => [],
                    ],

                    'cacheTwo' => [
                        'type' => 'memory',
                        'options' => [],
                    ],
                ],

                'fileSystems' => [
                    'one' => [
                        'adaptor' => 'adaptorOne',
                        'cache' => 'cacheOne',
                        'plugins' => []
                    ],

                    'two' => [
                        'adaptor' => 'adaptorTwo',
                        'cache' => 'cacheTwo',
                        'plugins' => []
                    ]
                ],
            ],
        ];
    }

    public function setup()
    {
        $this->container = $this->createMock(ContainerInterface::class);

        $this->container->expects($this->any())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $this->container->expects($this->any())
            ->method('get')
            ->with('config')
            ->willReturn($this->getConfig());

        $this->factory = new FlySystemManagerFactory();
    }

    public function testInvoke()
    {
        $manager = $this->factory->__invoke($this->container);
        $this->assertInstanceOf(FlySystemManager::class, $manager);
    }

    public function testGetConfig()
    {
        $mainConfig = $this->factory->getConfig($this->container);
        $this->assertInstanceOf(MainConfig::class, $mainConfig);
    }

    public function testGetCacheMapper()
    {
        $class = $this->factory->getCacheMapper($this->container);
        $this->assertInstanceOf(CacheMapper::class, $class);
    }

    public function testGetAdaptorMapper()
    {
        $class = $this->factory->getAdaptorMapper($this->container);
        $this->assertInstanceOf(AdaptorMapper::class, $class);
    }

    public function testGetAdaptorManager()
    {
        $class = $this->factory->getAdaptorManager($this->container);
        $this->assertInstanceOf(AdaptorManager::class, $class);
    }

    public function testGetCacheManager()
    {
        $class = $this->factory->getCacheManager($this->container);
        $this->assertInstanceOf(CacheManager::class, $class);
    }
}
