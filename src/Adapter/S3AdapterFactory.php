<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Visibility;

class S3AdapterFactory implements FactoryInterface, ContainerAwareInterface
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

        return new AwsS3V3Adapter($client, $bucket, $prefix, $permissions);
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
        $version = $options['version'] ?? 'latest';

        return new S3Client([
            'version'     => $version,
            'region'      => $region,
            'credentials' => [
                'key'    => $key,
                'secret' => $secret
            ]
        ]);
    }
}
