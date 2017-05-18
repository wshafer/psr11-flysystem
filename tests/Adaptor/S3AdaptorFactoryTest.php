<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use League\Flysystem\AwsS3v3\AwsS3Adapter;
use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Adaptor\S3AdapterFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\AzureAdapterFactory
 */
class S3AdaptorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $key = getenv('AWS_KEY');
        $secret = getenv('AWS_SECRET');

        if (!$key || !$secret) {
            $this->markTestSkipped('Missing needed keys to connect to s3.');
        }

        $factory = new S3AdapterFactory();
        $class = $factory([
            'key' => $key,
            'secret' => $secret,
            'region' => 'us-west-2',
            'bucket' => 'flysystemtester',
            'prefix' => 'test',
        ]);

        $this->assertInstanceOf(AwsS3Adapter::class, $class);
    }
}
