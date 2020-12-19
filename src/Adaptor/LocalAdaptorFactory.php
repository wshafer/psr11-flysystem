<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

class LocalAdaptorFactory implements FactoryInterface
{
    public function __invoke(array $options): LocalFilesystemAdapter
    {
        $root = $options['root'] ?? null;
        $writeFlags = $options['writeFlags'] ?? LOCK_EX;
        $linkHandling = $options['linkBehavior'] ?? LocalFilesystemAdapter::DISALLOW_LINKS;
        $permissions = PortableVisibilityConverter::fromArray($options['permissions'] ?? []);

        return new LocalFilesystemAdapter($root, $permissions, $writeFlags, $linkHandling);
    }
}
