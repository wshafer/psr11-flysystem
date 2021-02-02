<?php
declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Config;

use Blazon\PSR11FlySystem\Exception\MissingConfigException;
use PHPUnit\Framework\TestCase;
use Blazon\PSR11FlySystem\Config\Config;

/** @covers \Blazon\PSR11FlySystem\Config\Config */
class ConfigTest extends TestCase
{
    /** @var Config */
    protected $config;

    protected $settings;

    protected function setup(): void
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

    public function testGetOptionsWithNoOptions()
    {
        unset($this->settings['options']);
        $config = new Config($this->settings);
        $this->assertEquals([], $config->getOptions());
    }

    public function testGetTypeWithNoType()
    {
        $this->expectException(MissingConfigException::class);
        unset($this->settings['type']);
        $config = new Config($this->settings);
        $config->getType();
    }
}
