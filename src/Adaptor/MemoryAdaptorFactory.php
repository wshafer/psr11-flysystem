<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use League\Flysystem\Memory\MemoryAdapter;
use WShafer\PSR11FlySystem\FactoryInterface;

class MemoryAdaptorFactory implements FactoryInterface
{
    public function __invoke(array $options)
    {
        return new MemoryAdapter();
    }
}
