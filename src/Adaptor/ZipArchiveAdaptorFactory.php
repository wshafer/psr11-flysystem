<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use WShafer\PSR11FlySystem\FactoryInterface;

class ZipArchiveAdaptorFactory implements FactoryInterface
{
    public function __invoke(array $options)
    {
        $path = $options['path'] ?? null;
        return new ZipArchiveAdapter($path);
    }
}
