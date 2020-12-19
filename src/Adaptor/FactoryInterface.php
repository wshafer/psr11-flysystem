<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

interface FactoryInterface
{
    public function __invoke(array $options);
}
