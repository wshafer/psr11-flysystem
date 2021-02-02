<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use Psr\Container\ContainerInterface;

interface ContainerAwareInterface
{
    public function getContainer() : ContainerInterface;
    public function setContainer(ContainerInterface $container);
}
