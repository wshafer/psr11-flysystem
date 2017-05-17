<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Config;

use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Config\AdaptorConfig;
use WShafer\PSR11FlySystem\Exception\MissingConfigException;

class AdaptorConfigTest extends TestCase
{
    /** @var AdaptorConfig */
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

        $this->config = new AdaptorConfig($this->settings);
        $this->assertInstanceOf(AdaptorConfig::class, $this->config);
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
        new AdaptorConfig([]);
    }

    public function testFailWithNoType()
    {
        $this->expectException(MissingConfigException::class);
        unset($this->settings['type']);
        new AdaptorConfig($this->settings);
    }
}
