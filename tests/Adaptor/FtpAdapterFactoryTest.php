<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Adaptor;

use Blazon\PSR11FlySystem\Adapter\FtpAdapterFactory;
use League\Flysystem\Ftp\FtpAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blazon\PSR11FlySystem\Adapter\FtpAdapterFactory
 */
class FtpAdapterFactoryTest extends TestCase
{
    /** @var FtpAdapterFactory */
    protected $factory;

    protected function setUp(): void
    {
        $this->factory = new FtpAdapterFactory();
        $this->assertInstanceOf(FtpAdapterFactory::class, $this->factory);
    }

    public function testConstructor()
    {
    }

    public function testInvoke()
    {
        $result = ($this->factory)([]);
        $this->assertInstanceOf(FtpAdapter::class, $result);
    }
}
