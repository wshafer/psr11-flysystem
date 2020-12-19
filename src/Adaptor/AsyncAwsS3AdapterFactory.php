<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use AsyncAws\S3\S3Client;
use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use League\Flysystem\AsyncAwsS3\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use Psr\Container\ContainerInterface;

class AsyncAwsS3AdapterFactory implements FactoryInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    protected $container;

    public function __invoke(array $options): AsyncAwsS3Adapter
    {
        $bucket = $options['bucket'] ?? null;
        $prefix = $options['prefix'] ?? null;

        $permissions = new PortableVisibilityConverter(
            $options['dir_permissions'] ?? Visibility::PUBLIC
        );

        $client = $this->getClient($options);

        return new AsyncAwsS3Adapter($client, $bucket, $prefix, $permissions);
    }

    protected function getClient(array $options): S3Client
    {
        $container = $this->getContainer();

        if (!empty($options['client'])) {
            return $container->get($options['client']);
        }

        $key = $options['accessKeyId'] ?? null;
        $secret = $options['accessKeySecret'] ?? null;
        $region = $options['region'] ?? 'us-east-1';

        return new S3Client([
            'region' => $region,
            'accessKeyId' => $key,
            'accessKeySecret' => $secret
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
