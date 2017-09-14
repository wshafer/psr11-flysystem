<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem;

use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Exception\InvalidContainerException;

class FlySystemFactory
{
    /** @var string */
    protected $fileSystemName = 'default';

    /** @var FlySystemManager */
    protected static $flySystemManager;

    /**
     * @param ContainerInterface $container
     *
     * @return Filesystem|MountManager
     */
    public function __invoke(ContainerInterface $container)
    {
        $manager = static::getFlySystemManager($container);
        $fileSystemName = $this->getFileSystemName();
        return $manager->get($fileSystemName);
    }

    /**
     * Magic method for constructing FileSystems by service name
     *
     * @param $name
     * @param $arguments
     *
     * @return Filesystem|MountManager
     */
    public static function __callStatic($name, $arguments)
    {
        if (empty($arguments[0])
            || !$arguments[0] instanceof ContainerInterface
        ) {
            throw new InvalidContainerException(
                'Argument 0 must be an instance of a PSR-11 container'
            );
        }

        $factory = new static();
        $factory->setFileSystemName($name);
        return $factory($arguments[0]);
    }

    /**
     * @return string
     */
    public function getFileSystemName(): string
    {
        return $this->fileSystemName;
    }

    /**
     * @param string $fileSystemName
     */
    public function setFileSystemName(string $fileSystemName)
    {
        $this->fileSystemName = $fileSystemName;
    }

    public static function getFlySystemManager(ContainerInterface $container) : FlySystemManager
    {
        // @codeCoverageIgnoreStart
        if (!static::$flySystemManager) {
            $factory = new FlySystemManagerFactory();
            static::setFlySystemManager($factory($container));
        }
        // @codeCoverageIgnoreEnd

        return static::$flySystemManager;
    }

    public static function setFlySystemManager(FlySystemManager $flySystemManager)
    {
        static::$flySystemManager = $flySystemManager;
    }
}
