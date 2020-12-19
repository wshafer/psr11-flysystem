<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use League\Flysystem\FilesystemAdapter;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Exception\InvalidConfigException;

class AdaptorMapper implements MapperInterface
{
    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
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
                return FtpAdaptorFactory::class;
            case 'googlecloudstorage':
                return null;
            case 'local':
                return LocalAdaptorFactory::class;
            case 'memory':
            case 'inmemory':
                return MemoryAdaptorFactory::class;
            case 'sftp':
                return SftpAdaptorFactory::class;
            case 'zip':
                return ZipArchiveAdaptorFactory::class;
        }

        return null;
    }

    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get(string $type, array $options): FilesystemAdapter
    {
        if ($this->container->has($type)) {
            return $this->container->get($type);
        }

        $className = $this->getFactoryClassName($type);

        if (!$className) {
            throw new InvalidConfigException(
                'Unable to locate a factory by the name of: '.$type
            );
        }

        /** @var FactoryInterface $factory */
        $factory = new $className();

        if (!$factory instanceof FactoryInterface) {
            throw new InvalidConfigException(
                'Class '.$className.' must be an instance of '.FactoryInterface::class
            );
        }

        if ($factory instanceof ContainerAwareInterface) {
            $factory->setContainer($this->container);
        }

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
