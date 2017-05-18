<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Adaptor\ZipArchiveAdaptorFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\ZipArchiveAdaptorFactory
 */
class ZipArchiveAdaptorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $factory = new ZipArchiveAdaptorFactory();
        $class = $factory([
            'path' => '/tmp/test.zip'
        ]);

        $this->assertInstanceOf(ZipArchiveAdapter::class, $class);
    }
}
