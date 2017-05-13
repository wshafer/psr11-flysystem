<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Service;

use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Cache\CacheMapper;
use WShafer\PSR11FlySystem\Config\MainConfig;

class CacheManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var MainConfig $config */
        $config = $container->get(MainConfig::class);

        /** @var CacheMapper $cacheMapper */
        $cacheMapper = $container->get(CacheMapper::class);

        return new CacheManager($config, $cacheMapper, $container);
    }
}
