<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Stub;

use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\ContainerAwareInterface;
use WShafer\PSR11FlySystem\FactoryInterface;

class FactoryStub implements FactoryInterface, ContainerAwareInterface
{
    protected $container;

    public function __invoke(array $options)
    {
        return (object) $options;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
