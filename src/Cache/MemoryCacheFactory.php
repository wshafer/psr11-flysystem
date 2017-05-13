<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Cache;

use League\Flysystem\Cached\Storage\Memory;
use WShafer\PSR11FlySystem\FactoryInterface;

class MemoryCacheFactory implements FactoryInterface
{
    public function __invoke(array $options)
    {
        return new Memory();
    }
}
