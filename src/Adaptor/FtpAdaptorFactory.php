<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use League\Flysystem\Adapter\Ftp;
use WShafer\PSR11FlySystem\FactoryInterface;

class FtpAdaptorFactory implements FactoryInterface
{
    public function __invoke(array $options)
    {
        return new Ftp($options);
    }
}
