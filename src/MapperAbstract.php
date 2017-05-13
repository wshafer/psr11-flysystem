<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem;

use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Exception\InvalidConfigException;

abstract class MapperAbstract implements MapperInterface
{
    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get(string $type, array $options)
    {
        if ($this->container->has($type)) {
            return $this->container->get($type);
        }

        $className = $this->getFactoryClassName($type);

        if (!$className) {
            throw new InvalidConfigException(
                'Unable to locate a factory by the name of: '.$type
            );
        }

        /** @var FactoryInterface $factory */
        $factory = new $className();

        if (!$factory instanceof FactoryInterface) {
            throw new InvalidConfigException(
                'Class '.$className.' must be an instance of '.FactoryInterface::class
            );
        }

        if ($factory instanceof ContainerAwareInterface) {
            $factory->setContainer($this->container);
        }

        return $factory($options);
    }

    public function has(string $type)
    {
        if ($this->container->has($type)) {
            return true;
        }

        $className = $this->getFactoryClassName($type);

        if (!$className) {
            return false;
        }

        return true;
    }

    abstract public function getFactoryClassName(string $type);
}
