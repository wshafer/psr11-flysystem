<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Config;

use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Config\CacheConfig;
use WShafer\PSR11FlySystem\Exception\MissingConfigException;

class CacheConfigTest extends TestCase
{
    /** @var CacheConfig */
    protected $config;

    protected $settings;

    public function setup()
    {
        $this->settings = [
            'type' => 'local',
            'options' => [
                'src' => '/tmp'
            ],
        ];

        $this->config = new CacheConfig($this->settings);
        $this->assertInstanceOf(CacheConfig::class, $this->config);
    }

    public function testConstructor()
    {
    }

    public function testGetType()
    {
        $this->assertEquals($this->settings['type'], $this->config->getType());
    }

    public function testGetOptions()
    {
        $this->assertEquals($this->settings['options'], $this->config->getOptions());
    }

    public function testFailWithNoConfig()
    {
        $this->expectException(MissingConfigException::class);
        new CacheConfig([]);
    }

    public function testFailWithNoType()
    {
        $this->expectException(MissingConfigException::class);
        unset($this->settings['type']);
        new CacheConfig($this->settings);
    }
}
