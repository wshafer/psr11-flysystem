<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem;

use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Adaptor\AdaptorMapper;
use WShafer\PSR11FlySystem\Adaptor\MapperInterface;
use WShafer\PSR11FlySystem\Config\Config;
use WShafer\PSR11FlySystem\Exception\InvalidConfigException;
use WShafer\PSR11FlySystem\Exception\InvalidContainerException;

class FlySystemFactory
{
    protected $configKey = 'default';

    public function __construct(string $configKey = 'default')
    {
        $this->configKey = $configKey;
    }

    public function __invoke(ContainerInterface $container): Filesystem
    {
        $config = $this->getConfig($container);
        $mapper = $this->getMapper($container);

        $adaptor = $mapper->get($config->getType(), $config->getOptions());

        return new Filesystem($adaptor);
    }

    public function getMapper(ContainerInterface $container): MapperInterface
    {
        return new AdaptorMapper($container);
    }

    public function getConfig(ContainerInterface $container): Config
    {
        $config = $this->getConfigArray($container);

        if (empty($config[$this->configKey])) {
            throw new InvalidConfigException(
                "No config found for adaptor: " . $this->configKey
            );
        }

        return new Config($config[$this->configKey]);
    }

    protected function getConfigArray(ContainerInterface $container): array
    {
        // Symfony config is parameters. //
        if (method_exists($container, 'getParameter')
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

        return [];
    }

    public static function __callStatic($name, $arguments): Filesystem
    {
        if (empty($arguments[0])
            || !$arguments[0] instanceof ContainerInterface
        ) {
            throw new InvalidContainerException(
                'Argument 0 must be an instance of a PSR-11 container'
            );
        }

        $factory = new static($name);
        return $factory($arguments[0]);
    }
}
