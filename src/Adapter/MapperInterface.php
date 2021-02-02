<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

interface MapperInterface
{
    public function get(string $type, array $options);
    public function has(string $type);
    public function getFactoryClassName(string $type);
}
