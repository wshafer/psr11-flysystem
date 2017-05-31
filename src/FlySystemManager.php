<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem;

use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Config\FileSystemConfig;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\Exception\UnknownFileSystemException;
use WShafer\PSR11FlySystem\Exception\UnknownPluginException;

class FlySystemManager implements ContainerInterface
{
    /** @var MainConfig  */
    protected $config;

    /** @var ContainerInterface */
    protected $adaptorManager;

    /** @var ContainerInterface */
    protected $cacheManager;

    /** @var Filesystem[]|MountManager[] */
    protected $systems = [];

    /** @var ContainerInterface */
    protected $container;

    /**
     * Manager constructor.
     * @param MainConfig         $config
     * @param ContainerInterface $adaptorManager
     * @param ContainerInterface $cacheManager
     * @param ContainerInterface $container
     */
    public function __construct(
        MainConfig $config,
        ContainerInterface $adaptorManager,
        ContainerInterface $cacheManager,
        ContainerInterface $container
    ) {
        $this->config = $config;
        $this->adaptorManager = $adaptorManager;
        $this->cacheManager = $cacheManager;
        $this->container = $container;
    }

    /**
     * @param string $id
     *
     * @return Filesystem|MountManager
     */
    public function get($id)
    {
        if (key_exists($id, $this->systems)) {
            return $this->systems[$id];
        }

        $fileSystemConfig = $this->config->getFileSystemConfig($id);

        if (!$fileSystemConfig) {
            throw new UnknownFileSystemException(
                'Unable to locate file system '.$id.'.  Please check your configuration.'
            );
        }

        if (!$fileSystemConfig->isManager()) {
            return $this->getFileSystem($id, $fileSystemConfig);
        }

        $fileSystems = [];

        foreach ($fileSystemConfig->getFileSystems() as $name => $managerSystemConfig) {
            $fileSystems[$name] = $this->getFileSystem($name, $managerSystemConfig);
        }

        $manager = new MountManager($fileSystems);

        if ($fileSystemConfig->getPlugins()) {
            $this->setPlugins($manager, $fileSystemConfig->getPlugins());
        }

        $this->systems[$id] = $manager;
        return $this->systems[$id];
    }

    /**
     * @param $id
     * @param FileSystemConfig $fileSystemConfig
     * @return Filesystem
     */
    protected function getFileSystem($id, FileSystemConfig $fileSystemConfig)
    {
        $adaptor = $this->adaptorManager->get($fileSystemConfig->getAdaptor());
        $cache = $this->cacheManager->get($fileSystemConfig->getCache());
        $cachedAdaptor = new CachedAdapter($adaptor, $cache);
        $fileSystem = new Filesystem($cachedAdaptor);

        if ($fileSystemConfig->getPlugins()) {
            $this->setPlugins($fileSystem, $fileSystemConfig->getPlugins());
        }

        $this->systems[$id] = $fileSystem;
        return $fileSystem;
    }

    /**
     * @param FilesystemInterface|MountManager $filesystem
     * @param array $plugins
     */
    protected function setPlugins($filesystem, array $plugins = [])
    {
        foreach ($plugins as $plugin) {
            $this->addPlugin($filesystem, $plugin);
        }
    }

    /**
     * @param FilesystemInterface|MountManager $filesystem
     * @param string $pluginName
     */
    protected function addPlugin($filesystem, string $pluginName)
    {
        if (!$this->container->has($pluginName)) {
            throw new UnknownPluginException(
                'Unable to locate plugin service '.$pluginName.' in the container.  Please check your config.'
            );
        }

        $filesystem->addPlugin($this->container->get($pluginName));
    }

    public function has($id)
    {
        return $this->config->hasFileSystemConfig($id);
    }
}
