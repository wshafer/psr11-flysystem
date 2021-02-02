<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use League\Flysystem\FilesystemAdapter;
use Psr\Container\ContainerInterface;
use Blazon\PSR11FlySystem\Exception\InvalidConfigException;

class AdapterMapper implements MapperInterface
{
    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFactoryClassName(string $type): ?string
    {
        if (class_exists($type)) {
            return $type;
        }

        switch (strtolower($type)) {
            case 'asyncawss3':
                return AsyncAwsS3AdapterFactory::class;
            case 's3': // For BC
            case 'awss3v3':
                return S3AdapterFactory::class;
            case 'ftp':
                return FtpAdapterFactory::class;
            case 'googlecloudstorage':
                return GoogleCloudStorageAdapterFactory::class;
            case 'local':
                return LocalAdapterFactory::class;
            case 'memory':
            case 'inmemory':
                return MemoryAdapterFactory::class;
            case 'sftp':
                return SftpAdapterFactory::class;
            case 'zip':
                return ZipArchiveAdapterFactory::class;
        }

        return null;
    }

    public function get(string $type, array $options): FilesystemAdapter
    {
        if ($this->container->has($type)) {
            return $this->container->get($type);
        }

        $className = $this->getFactoryClassName($type);

        if (!$className) {
            throw new InvalidConfigException(
                'Unable to locate a factory by the name of: ' . $type
            );
        }

        if (!in_array(FactoryInterface::class, class_implements($className))) {
            throw new InvalidConfigException(
                'Class ' . $className . ' must be an instance of ' . FactoryInterface::class
            );
        }

        /** @var FactoryInterface $factory */
        /** @psalm-suppress InvalidStringClass */
        $factory = new $className();

        if ($factory instanceof ContainerAwareInterface) {
            $factory->setContainer($this->container);
        }

        // @codeCoverageIgnoreStart
        // Unreachable code in tests
        if (!is_callable($factory)) {
            throw new InvalidConfigException(
                'Class ' . $className . ' must be callable.'
            );
        }
        // @codeCoverageIgnoreEnd

        return $factory($options);
    }

    public function has(string $type): bool
    {
        if ($this->container->has($type)) {
            return true;
        }

        $className = $this->getFactoryClassName($type);

        if (!$className) {
            return false;
        }

        return true;
    }
}
