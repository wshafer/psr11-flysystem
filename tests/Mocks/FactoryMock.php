<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Mocks;

use Blazon\PSR11FlySystem\Adapter\ContainerAwareInterface;
use Blazon\PSR11FlySystem\Adapter\FactoryInterface;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Psr\Container\ContainerInterface;

class FactoryMock implements FactoryInterface, ContainerAwareInterface
{
    public static $container = null;

    public function __invoke(array $options): FilesystemAdapter
    {
        return new InMemoryFilesystemAdapter();
    }

    public function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    public function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }
}
