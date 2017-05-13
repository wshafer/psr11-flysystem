<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use League\Flysystem\Adapter\NullAdapter;
use WShafer\PSR11FlySystem\FactoryInterface;

class NullAdaptorFactory implements FactoryInterface
{
    public function __invoke(array $options)
    {
        return new NullAdapter();
    }
}
