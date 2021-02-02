<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use Psr\Container\ContainerInterface;

trait ContainerTrait
{
    /** @var ContainerInterface */
    protected $container;

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
