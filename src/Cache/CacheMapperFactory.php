<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Cache;

use Psr\Container\ContainerInterface;

class CacheMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new CacheMapper($container);
    }
}
