<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Service;

use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Config\FileSystemConfig;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\Exception\UnknownFileSystemException;
use WShafer\PSR11FlySystem\Exception\UnknownPluginException;

class FileSystemManager implements ContainerInterface
{
    protected $config = [];

    /** @var AdaptorManager */
    protected $adaptorManager;

    /** @var CacheManager */
    protected $cacheManager;

    /** @var Filesystem[]|MountManager[] */
    protected $systems = [];

    /** @var ContainerInterface */
    protected $container;

    /**
     * Manager constructor.
     * @param MainConfig         $config
     * @param AdaptorManager     $adaptorManager
     * @param CacheManager       $cacheManager
     * @param ContainerInterface $container
     */
    public function __construct(
        MainConfig $config,
        AdaptorManager $adaptorManager,
        CacheManager $cacheManager,
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
        if (!$this->has($id)) {
            throw new UnknownFileSystemException(
                'Unable to locate file system '.$id.'.  Please check your configuration.'
            );
        }

        if (key_exists($id, $this->systems)) {
            return $this->systems[$id];
        }

        $fileSystemConfig = $this->config->getFileSystemConfig($id);

        if (!$fileSystemConfig->isManager()) {
            return $this->getFileSystem($id, $fileSystemConfig);
        }

        $fileSystems = [];

        foreach ($fileSystemConfig->getFileSystems() as $name => $managerFileSystemConfig) {
            $fileSystems[] = $this->getFileSystem($name, $managerFileSystemConfig);
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
        return $this->systems[$id];
    }

    /**
     * @param FilesystemInterface|MountManager $filesystem
     * @param array $plugins
     */
    protected function setPlugins($filesystem, array $plugins = [])
    {
        if (empty($plugins)) {
            return;
        }

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

    public function getConfig()
    {
        return $this->config;
    }
}
