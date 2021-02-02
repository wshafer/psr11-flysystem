<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use Blazon\PSR11FlySystem\Exception\MissingConfigException;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\ZipArchive\FilesystemZipArchiveProvider;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

class ZipArchiveAdapterFactory implements FactoryInterface
{
    public function __invoke(array $options): FilesystemAdapter
    {
        $path = $options['path'] ?? null;

        if (!$path) {
            throw new MissingConfigException(
                "Zip config missing path."
            );
        }

        return new ZipArchiveAdapter(new FilesystemZipArchiveProvider($path));
    }
}
