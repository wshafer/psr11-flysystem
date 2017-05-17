<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Config;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\Config\MainConfigFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Config\MainConfigFactory
 */
class MainConfigFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $config = [
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

        /** @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $container->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $factory = new MainConfigFactory();
        $mainConfig = $factory($container);
        $this->assertInstanceOf(MainConfig::class, $mainConfig);
    }
}
