<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem;

class ConfigProvider
{
    public function __invoke()
    {
        return require __DIR__.'/../config/flysystem.config.php';
    }
}
