<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Cache;

use League\Flysystem\Cached\Storage\Psr6Cache;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\ContainerAwareInterface;
use WShafer\PSR11FlySystem\Exception\MissingServiceException;
use WShafer\PSR11FlySystem\FactoryInterface;

class Psr6CacheFactory implements FactoryInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    protected $container;

    public function __invoke(array $options)
    {
        $serviceName = $options['service'] ?? null;
        $key = $options['key'] ?? 'flysystem';
        $ttl = $options['ttl'] ?? null;

        $service = $this->getService($serviceName);
        return new Psr6Cache($service, $key, $ttl);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function getService($serviceName)
    {
        if (empty($serviceName)
            || !$this->getContainer()->has($serviceName)
        ) {
            throw new MissingServiceException(
                'Unable to locate service '.$serviceName
            );
        }

        return $this->getContainer()->get($serviceName);
    }
}
