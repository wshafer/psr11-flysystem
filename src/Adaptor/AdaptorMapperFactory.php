<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use Psr\Container\ContainerInterface;

class AdaptorMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AdaptorMapper($container);
    }
}
