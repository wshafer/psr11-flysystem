<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use PHPUnit\Framework\TestCase;
use Spatie\FlysystemDropbox\DropboxAdapter;
use WShafer\PSR11FlySystem\Adaptor\DropBoxAdapterFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\DropBoxAdapterFactory
 */
class DropBoxAdaptorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $token = getenv('DROPBOX_TOKEN');

        if (!$token) {
            $this->markTestSkipped('Missing needed token to connect to DropBox.');
        }

        $factory = new DropBoxAdapterFactory();
        $class = $factory([
            'token' => $token,
            'prefix' => 'test',
        ]);

        $this->assertInstanceOf(DropboxAdapter::class, $class);
    }
}
