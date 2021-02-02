<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem;

use Blazon\PSR11FlySystem\Exception\MissingConfigException;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;
use Blazon\PSR11FlySystem\Adapter\AdapterMapper;
use Blazon\PSR11FlySystem\Adapter\MapperInterface;
use Blazon\PSR11FlySystem\Config\Config;
use Blazon\PSR11FlySystem\Exception\InvalidConfigException;
use Blazon\PSR11FlySystem\Exception\InvalidContainerException;

class FlySystemFactory
{
    protected $configKey = 'default';

    public static function __callStatic($name, $arguments): Filesystem
    {
        if (
            empty($arguments[0])
            || !$arguments[0] instanceof ContainerInterface
        ) {
            throw new InvalidContainerException(
                'Argument 0 must be an instance of a PSR-11 container'
            );
        }

        $factory = new self($name);
        return $factory($arguments[0]);
    }

    public function __construct(string $configKey = 'default')
    {
        $this->configKey = $configKey;
    }

    public function __invoke(ContainerInterface $container): Filesystem
    {
        $config = $this->getConfig($container);
        $mapper = $this->getMapper($container);
        $adapter = $mapper->get($config->getType(), $config->getOptions());

        return new Filesystem($adapter);
    }

    public function getMapper(ContainerInterface $container): MapperInterface
    {
        return new AdapterMapper($container);
    }

    public function getConfig(ContainerInterface $container): Config
    {
        $config = $this->getConfigArray($container);

        if (empty($config['flysystem'][$this->configKey])) {
            throw new InvalidConfigException(
                "No config found for adapter: " . $this->configKey
            );
        }

        return new Config($config['flysystem'][$this->configKey]);
    }

    public function getConfigArray(ContainerInterface $container): array
    {
        // Symfony config is parameters. //
        if (
            method_exists($container, 'getParameter')
            && method_exists($container, 'hasParameter')
            && $container->hasParameter('flysystem')
        ) {
            return ['flysystem' => $container->getParameter('flysystem')];
        }

        // Zend uses config key
        if ($container->has('config')) {
            return $container->get('config');
        }

        // Slim Config comes from "settings"
        if ($container->has('settings')) {
            return ['flysystem' => $container->get('settings')['flysystem']];
        }

        throw new MissingConfigException("Unable to locate FlySystem configuration");
    }
}
