<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use League\Flysystem\Sftp\SftpAdapter;
use WShafer\PSR11FlySystem\FactoryInterface;

class SftpAdaptorFactory implements FactoryInterface
{
    public function __invoke(array $options)
    {
        return new SftpAdapter($options);
    }
}
