<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Service;

use League\Flysystem\Cached\CacheInterface;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\MapperInterface;

class CacheManager implements ContainerInterface
{
    protected $config = [];

    /** @var MapperInterface */
    protected $cacheMapper;

    /** @var ContainerInterface */
    protected $container;

    /**
     * Manager constructor.
     * @param MainConfig         $config
     * @param MapperInterface    $cacheMapper
     * @param ContainerInterface $container
     */
    public function __construct(
        MainConfig $config,
        MapperInterface $cacheMapper,
        ContainerInterface $container
    ) {
        $this->config = $config;
        $this->cacheMapper = $cacheMapper;
        $this->container = $container;
    }

    /**
     * @param string $id
     *
     * @return CacheInterface
     */
    public function get($id)
    {
        $cacheConfig = $this->getConfig()->getCacheConfig($id);
        return $this->cacheMapper->get(
            $cacheConfig->getType(),
            $cacheConfig->getOptions()
        );
    }

    public function has($id)
    {
        return $this->config->hasCacheConfig($id);
    }

    public function getConfig()
    {
        return $this->config;
    }
}
