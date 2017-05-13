<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use League\Flysystem\Adapter\Local;
use WShafer\PSR11FlySystem\FactoryInterface;

class LocalAdaptorFactory implements FactoryInterface
{
    public function __invoke(array $options)
    {
        $root = $options['root'] ?? null;
        $writeFlags = $options['writeFlags'] ?? LOCK_EX;
        $linkHandling = $options['linkBehavior'] ?? Local::DISALLOW_LINKS;
        $permissions = $options['permissions'] ?? [];

        return new Local(
            $root,
            $writeFlags,
            $linkHandling,
            $permissions
        );
    }
}
