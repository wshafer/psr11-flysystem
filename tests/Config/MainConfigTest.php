<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Config;

use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Config\AdaptorConfig;
use WShafer\PSR11FlySystem\Config\CacheConfig;
use WShafer\PSR11FlySystem\Config\FileSystemConfig;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\Exception\MissingConfigException;

/**
 * @covers \WShafer\PSR11FlySystem\Config\MainConfig
 */
class MainConfigTest extends TestCase
{
    /** @var MainConfig */
    protected $config;

    protected $settings;

    public function setup()
    {
        $this->settings = [
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

        $this->config = new MainConfig($this->settings);
        $this->assertInstanceOf(MainConfig::class, $this->config);
    }

    public function testConstructor()
    {
    }

    public function testHasAdaptorConfig()
    {
        $this->assertTrue(
            $this->config->hasAdaptorConfig('adaptorOne')
        );
    }

    public function testHasAdaptorConfigReturnsNull()
    {
        $this->assertFalse(
            $this->config->hasAdaptorConfig('notHere')
        );
    }

    public function testHasCacheConfig()
    {
        $this->assertTrue(
            $this->config->hasCacheConfig('cacheOne')
        );
    }

    public function testHasCacheConfigReturnsNull()
    {
        $this->assertFalse(
            $this->config->hasCacheConfig('notHere')
        );
    }

    public function testHasFileSystemConfig()
    {
        $this->assertTrue(
            $this->config->hasFileSystemConfig('one')
        );
    }

    public function testHasFileSystemConfigReturnsNull()
    {
        $this->assertFalse(
            $this->config->hasFileSystemConfig('notHere')
        );
    }

    public function testGetAdaptorConfig()
    {
        $this->assertInstanceOf(
            AdaptorConfig::class,
            $this->config->getAdaptorConfig('adaptorOne')
        );
    }

    public function testGetAdaptorConfigReturnsNull()
    {
        $this->assertNull(
            $this->config->getAdaptorConfig('notHere')
        );
    }

    public function testGetCacheConfig()
    {
        $this->assertInstanceOf(
            CacheConfig::class,
            $this->config->getCacheConfig('cacheOne')
        );
    }

    public function testGetCacheConfigReturnsNull()
    {
        $this->assertNull(
            $this->config->getCacheConfig('notHere')
        );
    }

    public function testGetFileSystemConfig()
    {
        $this->assertInstanceOf(
            FileSystemConfig::class,
            $this->config->getFileSystemConfig('one')
        );
    }

    public function testGetFileSystemConfigReturnsNull()
    {
        $this->assertNull(
            $this->config->getFileSystemConfig('notHere')
        );
    }

    public function testFailWithNoConfig()
    {
        $this->expectException(MissingConfigException::class);
        new MainConfig([]);
    }

    public function testFailWithNoMainKey()
    {
        $this->expectException(MissingConfigException::class);
        unset($this->settings['flysystem']);
        new MainConfig($this->settings);
    }

    public function testFailWithNoFileSystems()
    {
        $this->expectException(MissingConfigException::class);
        unset($this->settings['flysystem']['fileSystems']);
        new MainConfig($this->settings);
    }

    public function testFailWithNoAdaptors()
    {
        $this->expectException(MissingConfigException::class);
        unset($this->settings['flysystem']['adaptors']);
        new MainConfig($this->settings);
    }
}
