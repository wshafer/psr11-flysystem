<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Config;

use WShafer\PSR11FlySystem\Exception\MissingConfigException;

class FileSystemConfig
{
    /** @var array  */
    protected $fileSystems = [];

    /** @var array  */
    protected $config = [];

    /**
     * FileSystemConfig constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        if ($this->isManager()) {
            $this->buildManagerFileSystemConfigs();
        }
    }

    /**
     * Get the adaptor
     *
     * @return string
     */
    public function getAdaptor()
    {
        return $this->config['adaptor'] ?? 'default';
    }

    /**
     * Get the cache
     *
     * @return string
     */
    public function getCache()
    {
        return $this->config['cache'] ?? 'default';
    }

    /**
     * Get the plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->config['plugins'] ?? [];
    }

    /**
     * Is this defined as a file manager?
     *
     * @return bool
     */
    public function isManager()
    {
        if ($this->getAdaptor() == 'manager') {
            return true;
        }

        return false;
    }

    /**
     * Get the configured File Systems.
     * Used for manager configuration
     *
     * @return FileSystemConfig[]
     */
    public function getFileSystems()
    {
        return $this->fileSystems;
    }

    /**
     * Build out the file system configs for use
     * by the manager.
     */
    protected function buildManagerFileSystemConfigs()
    {
        if (empty($this->config['fileSystems'])) {
            throw new MissingConfigException(
                'Missing file systems for manager'
            );
        }

        foreach ($this->config['fileSystems'] as $name => $fileSystem) {
            $this->fileSystems[$name] = new self($fileSystem);
        }
    }
}
