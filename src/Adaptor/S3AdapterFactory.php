<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use Psr\Container\ContainerInterface;

class S3AdapterFactory implements FactoryInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    protected $container;

    public function __invoke(array $options): AwsS3V3Adapter
    {
        $bucket = $options['bucket'] ?? null;
        $prefix = $options['prefix'] ?? null;

        $permissions = new PortableVisibilityConverter(
            $options['dir_permissions'] ?? Visibility::PUBLIC
        );

        $client = $this->getClient($options);

        return new AwsS3V3Adapter($client, $bucket, $prefix, $permissions);
    }

    protected function getClient(array $options): S3Client
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

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
