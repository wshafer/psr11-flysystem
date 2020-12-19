<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Config;

use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Config\Config;
use WShafer\PSR11FlySystem\Exception\MissingConfigException;

class AdaptorConfigTest extends TestCase
{
    /** @var Config */
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

        $this->config = new Config($this->settings);
        $this->assertInstanceOf(Config::class, $this->config);
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
        new Config([]);
    }

    public function testFailWithNoType()
    {
        $this->expectException(MissingConfigException::class);
        unset($this->settings['type']);
        new Config($this->settings);
    }
}
