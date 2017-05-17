<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Stub;

use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\MapperAbstract;

class MapperStub extends MapperAbstract
{
    public function getFactoryClassName(string $type)
    {
        if (class_exists($type)) {
            return $type;
        }

        return null;
    }
}
