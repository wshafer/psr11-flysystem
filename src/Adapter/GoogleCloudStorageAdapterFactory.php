<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\GoogleCloudStorage\PortableVisibilityHandler;
use League\Flysystem\Visibility;

class GoogleCloudStorageAdapterFactory implements FactoryInterface, ContainerAwareInterface
{
    use ContainerTrait;

    public function __invoke(array $options): FilesystemAdapter
    {
        $bucket = $options['bucket'] ?? null;
        $client = $options['client'] ?? null;
        $prefix = $options['prefix'] ?? '';
        $defaultVisibility = $options['defaultVisibility'] ?? Visibility::PRIVATE;

        $permissions = [];

        if (
            !empty($options['permissions'])
            && is_array($options['permissions'])
        ) {
            $permissions = $options['permissions'];
        }

        $clientOptions = [];

        if (
            !empty($options['clientOptions'])
            && is_array($options['clientOptions'])
        ) {
            $clientOptions = $options['clientOptions'];
        }

        $bucket = $this->getBucket(
            $bucket,
            $clientOptions,
            $client
        );

        $visibilityHandler = $this->getVisibilityHandler($permissions);

        return new GoogleCloudStorageAdapter(
            $bucket,
            $prefix,
            $visibilityHandler,
            $defaultVisibility
        );
    }

    public function getBucket(
        string $name,
        array $clientOptions,
        ?string $client = null
    ): Bucket {
        $container = $this->getContainer();

        if ($container->has($name)) {
            return $container->get($name);
        }

        $client = $this->getClient($clientOptions, $client);

        return $client->bucket($name);
    }

    public function getClient(array $clientOptions, ?string $name = null): StorageClient
    {
        if (!$name) {
            return new StorageClient($clientOptions);
        }

        return $this->getContainer()->get($name);
    }

    public function getVisibilityHandler(array $permissions): PortableVisibilityHandler
    {
        $entity = $permissions['entity'] ?? 'allUsers';
        $publicAcl = $permissions['publicAcl'] ?? PortableVisibilityHandler::ACL_PUBLIC_READ;
        $privateAcl = $permissions['privateAcl'] ?? PortableVisibilityHandler::ACL_PROJECT_PRIVATE;

        return new PortableVisibilityHandler(
            $entity,
            $publicAcl,
            $privateAcl
        );
    }
}
