<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;

class MemoryAdapterFactory implements FactoryInterface
{
    public function __invoke(array $options): FilesystemAdapter
    {
        return new InMemoryFilesystemAdapter();
    }
}
