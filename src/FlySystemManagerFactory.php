<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem;

use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Adaptor\AdaptorMapper;
use WShafer\PSR11FlySystem\Cache\CacheMapper;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\Service\AdaptorManager;
use WShafer\PSR11FlySystem\Service\CacheManager;

class FlySystemManagerFactory
{
    protected $config;

    public function __invoke(ContainerInterface $container)
    {
        /** @var MainConfig $config */
        $config = $this->getConfig($container);

        /** @var AdaptorManager $adaptorManager */
        $adaptorManager = $this->getAdaptorManager($container);

        /** @var CacheManager $cacheManager */
        $cacheManager = $this->getCacheManager($container);

        return new FlySystemManager($config, $adaptorManager, $cacheManager, $container);
    }

    public function getConfig(ContainerInterface $container)
    {
        if (!$this->config) {
            $config = $this->getConfigArray($container);
            $this->config = new MainConfig($config);
        }

        return $this->config;
    }

    protected function getConfigArray(ContainerInterface $container)
    {
        // Symfony config is parameters. //
        if (method_exists($container, 'getParameter')
            && method_exists($container, 'hasParameter')
            && $container->hasParameter('flysystem')
        ) {
            return ['flysystem' => $container->getParameter('flysystem')];
        }

        // Zend uses config key
        if ($container->has('config')) {
            return $container->get('config');
        }

        // Slim Config comes from "settings"
        if ($container->has('settings')) {
            return ['flysystem' => $container->get('settings')['flysystem']];
        }

        return [];
    }

    public function getAdaptorManager(ContainerInterface $container)
    {
        /** @var MainConfig $config */
        $config = $this->getConfig($container);

        /** @var AdaptorMapper $adaptorMapper */
        $adaptorMapper = $this->getAdaptorMapper($container);

        return new AdaptorManager($config, $adaptorMapper, $container);
    }

    public function getAdaptorMapper(ContainerInterface $container)
    {
        return new AdaptorMapper($container);
    }

    public function getCacheManager(ContainerInterface $container)
    {
        /** @var MainConfig $config */
        $config = $this->getConfig($container);

        /** @var CacheMapper $cacheMapper */
        $cacheMapper = $this->getCacheMapper($container);

        return new CacheManager($config, $cacheMapper, $container);
    }

    public function getCacheMapper(ContainerInterface $container)
    {
        return new CacheMapper($container);
    }
}
