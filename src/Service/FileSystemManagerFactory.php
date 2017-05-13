<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Service;

use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Config\MainConfig;

class FileSystemManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var MainConfig $config */
        $config = $container->get(MainConfig::class);

        /** @var AdaptorManager $adaptorManager */
        $adaptorManager = $container->get(AdaptorManager::class);

        /** @var CacheManager $cacheManager */
        $cacheManager = $container->get(CacheManager::class);

        return new FileSystemManager($config, $adaptorManager, $cacheManager, $container);
    }
}
