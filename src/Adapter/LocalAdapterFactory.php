<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use Blazon\PSR11FlySystem\Exception\MissingConfigException;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

class LocalAdapterFactory implements FactoryInterface
{
    public function __invoke(array $options): FilesystemAdapter
    {
        $root = $options['root'] ?? null;

        if (empty($root)) {
            throw new MissingConfigException("Local Adapter missing root path");
        }

        $writeFlags = $options['writeFlags'] ?? LOCK_EX;
        $linkHandling = $options['linkBehavior'] ?? LocalFilesystemAdapter::DISALLOW_LINKS;
        $permissions = PortableVisibilityConverter::fromArray($options['permissions'] ?? []);

        return new LocalFilesystemAdapter($root, $permissions, $writeFlags, $linkHandling);
    }
}
