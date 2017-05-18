<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use League\Flysystem\Adapter\Ftp;
use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Adaptor\FtpAdaptorFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\FtpAdaptorFactory
 */
class FtpAdaptorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $factory = new FtpAdaptorFactory();
        $class = $factory([
            'host' => 'ftp.example.com',
            'username' => 'username',
            'password' => 'password',
        ]);

        $this->assertInstanceOf(Ftp::class, $class);
    }
}
