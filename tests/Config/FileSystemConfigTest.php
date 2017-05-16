<?php

namespace WShafer\PSR11FlySystem\Test\Config;

use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Config\FileSystemConfig;
use WShafer\PSR11FlySystem\Exception\MissingConfigException;

class FileSystemConfigTest extends TestCase
{
    /** @var FileSystemConfig */
    protected $config;

    /** @var FileSystemConfig */
    protected $managerConfig;

    protected $settings;

    protected $managerSettings;

    public function setupFileSystemConfig()
    {
        $this->settings = [
            'adaptor' => 'local',
            'cache' => 'memory',
            'plugins' => [
                'pluginOne' => 'SomeServiceHere'
            ]
        ];

        $this->config = new FileSystemConfig($this->settings);
        $this->assertInstanceOf(FileSystemConfig::class, $this->config);
    }

    public function setupManager()
    {
        $this->managerSettings = [
            'adaptor' => 'manager',
            'fileSystems' => [
                'memory' => [
                    'adaptor' => 'null',
                    'cache' => 'memory',
                    'plugins' => [
                        'pluginOne' => 'SomeServiceHere'
                    ]
                ],
                'local' => [
                    'adaptor' => 'local',
                    'cache' => 'psr6',
                    'plugins' => [
                        'pluginTwo' => 'SomeServiceHere'
                    ]
                ]
            ]
        ];

        $this->managerConfig = new FileSystemConfig($this->managerSettings);
        $this->assertInstanceOf(FileSystemConfig::class, $this->managerConfig);
    }

    public function testConstructor()
    {
        $this->setupFileSystemConfig();
    }

    public function testGetCache()
    {
        $this->setupFileSystemConfig();
        $this->assertEquals($this->settings['cache'], $this->config->getCache());
    }

    public function testPlugins()
    {
        $this->setupFileSystemConfig();
        $this->assertEquals($this->settings['plugins'], $this->config->getPlugins());
    }

    public function testIsManager()
    {
        $this->setupManager();
        $this->assertTrue($this->managerConfig->isManager());
    }

    public function testGetFileSystemsFromManager()
    {
        $this->setupManager();

        $fileSystems = $this->managerConfig->getFileSystems();

        $this->assertEquals(
            count($this->managerSettings['fileSystems']),
            count($fileSystems)
        );

        foreach ($fileSystems as $fileSystem) {
            $this->assertInstanceOf(FileSystemConfig::class, $fileSystem);
        }
    }

    public function testManagerMissingFileSystems()
    {
        $this->expectException(MissingConfigException::class);

        new FileSystemConfig([
            'adaptor' => 'manager'
        ]);
    }

    public function testFailWithNoConfig()
    {
        $this->expectException(MissingConfigException::class);
        new FileSystemConfig([]);
    }

    public function testFailWithNoType()
    {
        $this->expectException(MissingConfigException::class);
        $this->setupFileSystemConfig();
        unset($this->settings['adaptor']);
        new FileSystemConfig($this->settings);
    }
}
