<?php

namespace WShafer\PSR11FlySystem;

class Module
{
    public function __invoke()
    {
        return require __DIR__.'/../../config/flysystem.config.php';
    }
}
