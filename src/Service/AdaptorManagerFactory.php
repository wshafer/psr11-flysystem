<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Service;

use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Adaptor\AdaptorMapper;
use WShafer\PSR11FlySystem\Config\MainConfig;

class AdaptorManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var MainConfig $config */
        $config = $container->get(MainConfig::class);

        /** @var AdaptorMapper $adaptorMapper */
        $adaptorMapper = $container->get(AdaptorMapper::class);

        return new AdaptorManager($config, $adaptorMapper, $container);
    }
}
