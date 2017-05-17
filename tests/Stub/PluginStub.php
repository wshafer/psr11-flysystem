<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Stub;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\PluginInterface;

class PluginStub implements PluginInterface
{
    public function handle()
    {
        return true;
    }

    public function getMethod()
    {
        return true;
    }

    public function setFilesystem(FilesystemInterface $filesystem)
    {
        return true;
    }
}
