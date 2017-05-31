<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Config;

use WShafer\PSR11FlySystem\Exception\MissingConfigException;

class MainConfig
{
    protected $config = [];

    protected $adaptors = [];

    protected $cache = [];

    protected $fileSystems = [];

    public function __construct(array $config)
    {
        $this->validateConfig($config);
        $this->config = $config;
        $this->buildAdaptorConfigs();
        $this->buildFileSystemConfigs();
        $this->buildCacheConfigs();
    }

    public function validateConfig($config)
    {
        if (empty($config)
            || empty($config['flysystem'])
        ) {
            throw new MissingConfigException(
                'No config key of "flysystem" found in config array.'
            );
        }

        if (empty($config['flysystem']['adaptors'])) {
            throw new MissingConfigException(
                'No config key of "adaptors" found in flysystem config array.'
            );
        }

        if (empty($config['flysystem']['fileSystems'])) {
            throw new MissingConfigException(
                'No config key of "adaptors" found in flysystem config array.'
            );
        }
    }

    /**
     * @param $fileSystem
     * @return FileSystemConfig|null
     */
    public function getFileSystemConfig($fileSystem)
    {
        return $this->fileSystems[$fileSystem] ?? null;
    }

    /**
     * @param $adaptor
     * @return AdaptorConfig|null
     */
    public function getAdaptorConfig($adaptor)
    {
        return $this->adaptors[$adaptor] ?? null;
    }

    /**
     * @param $cache
     *
     * @return CacheConfig|null
     */
    public function getCacheConfig($cache)
    {
        return $this->cache[$cache] ?? null;
    }

    public function hasFileSystemConfig($fileSystem) : bool
    {
        return key_exists($fileSystem, $this->fileSystems);
    }

    public function hasAdaptorConfig($adaptor) : bool
    {
        return key_exists($adaptor, $this->adaptors);
    }

    public function hasCacheConfig($cache) : bool
    {
        return key_exists($cache, $this->cache);
    }

    protected function buildAdaptorConfigs()
    {
        foreach ($this->config['flysystem']['adaptors'] as $name => $adaptor) {
            $this->adaptors[$name] = new AdaptorConfig($adaptor);
        }
    }

    protected function buildCacheConfigs()
    {
        if (empty($this->config['flysystem']['caches'])) {
            return;
        }

        foreach ($this->config['flysystem']['caches'] as $name => $cache) {
            $this->cache[$name] = new CacheConfig($cache);
        }
    }

    protected function buildFileSystemConfigs()
    {
        foreach ($this->config['flysystem']['fileSystems'] as $name => $fileSystem) {
            $this->fileSystems[$name] = new FileSystemConfig($fileSystem);
        }
    }
}
