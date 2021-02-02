<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use League\Flysystem\FilesystemAdapter;

interface FactoryInterface
{
    public function __invoke(array $options): FilesystemAdapter;
}
