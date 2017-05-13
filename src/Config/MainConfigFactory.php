<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Config;

use Psr\Container\ContainerInterface;

class MainConfigFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];
        return new MainConfig($config);
    }
}
