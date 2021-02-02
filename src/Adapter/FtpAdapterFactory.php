<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;

class FtpAdapterFactory implements FactoryInterface
{
    public function __invoke(array $options): FilesystemAdapter
    {
        return new FtpAdapter(FtpConnectionOptions::fromArray($options));
    }
}
