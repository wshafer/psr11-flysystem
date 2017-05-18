<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use WShafer\PSR11FlySystem\FactoryInterface;

class S3AdapterFactory implements FactoryInterface
{
    public function __invoke(array $options)
    {
        $key = $options['key'] ?? null;
        $secret = $options['secret'] ?? null;
        $region = $options['region'] ?? 'us-east-1';
        $version = $options['version'] ?? 'latest';
        $bucket = $options['bucket'] ?? null;
        $prefix = $options['prefix'] ?? null;

        $client = new S3Client([
            'version'     => $version,
            'region'      => $region,
            'credentials' => [
                'key'    => $key,
                'secret' => $secret
            ]
        ]);

        return new AwsS3Adapter($client, $bucket, $prefix);
    }
}
