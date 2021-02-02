<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use AsyncAws\S3\S3Client;
use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use League\Flysystem\AsyncAwsS3\PortableVisibilityConverter;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Visibility;

class AsyncAwsS3AdapterFactory implements FactoryInterface, ContainerAwareInterface
{
    use ContainerTrait;

    public function __invoke(array $options): FilesystemAdapter
    {
        $bucket = $options['bucket'] ?? '';
        $prefix = $options['prefix'] ?? '';

        $permissions = new PortableVisibilityConverter(
            $options['dirPermissions'] ?? Visibility::PUBLIC
        );

        $client = $this->getClient($options);

        return new AsyncAwsS3Adapter($client, $bucket, $prefix, $permissions);
    }

    public function getClient(array $options): S3Client
    {
        $container = $this->getContainer();

        if (!empty($options['client'])) {
            return $container->get($options['client']);
        }

        $key = $options['key'] ?? null;
        $secret = $options['secret'] ?? null;
        $region = $options['region'] ?? 'us-east-1';

        return new S3Client([
            'region' => $region,
            'accessKeyId' => $key,
            'accessKeySecret' => $secret
        ]);
    }
}
