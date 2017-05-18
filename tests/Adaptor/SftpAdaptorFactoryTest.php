<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use League\Flysystem\Sftp\SftpAdapter;
use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Adaptor\SftpAdaptorFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\FtpAdaptorFactory
 */
class SftpAdaptorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $factory = new SftpAdaptorFactory();
        $class = $factory([]);

        $this->assertInstanceOf(SftpAdapter::class, $class);
    }
}
