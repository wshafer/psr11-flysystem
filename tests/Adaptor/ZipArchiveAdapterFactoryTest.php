<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Adaptor;

use Blazon\PSR11FlySystem\Adapter\ZipArchiveAdapterFactory;
use Blazon\PSR11FlySystem\Exception\MissingConfigException;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Blazon\PSR11FlySystem\Adapter\ZipArchiveAdapterFactory
 */
class ZipArchiveAdapterFactoryTest extends TestCase
{
    /** @var ZipArchiveAdapterFactory */
    protected $factory;

    protected function setUp(): void
    {
        $this->factory = new ZipArchiveAdapterFactory();
        $this->assertInstanceOf(ZipArchiveAdapterFactory::class, $this->factory);
    }

    public function testConstructor()
    {
    }

    public function testInvoke()
    {
        $path = '/tmp';
        $result = ($this->factory)(['path' => $path]);
        $this->assertInstanceOf(ZipArchiveAdapter::class, $result);
    }

    public function testInvokeMissingPath()
    {
        $this->expectException(MissingConfigException::class);
        ($this->factory)([]);
    }
}
