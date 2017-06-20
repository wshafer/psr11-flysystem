<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Cache;

use League\Flysystem\Cached\Storage\Adapter;
use WShafer\PSR11FlySystem\Exception\MissingConfigException;
use WShafer\PSR11FlySystem\Exception\MissingServiceException;
use WShafer\PSR11FlySystem\FlySystemManager;

class AdaptorCacheFactory extends ContainerAwareCacheAbstract
{
    public function __invoke(array $options)
    {
        $flyManagerServiceName = $options['flyManagerServiceName'] ?? FlySystemManager::class;

        if (empty($options['fileSystem'])) {
            throw new MissingConfigException(
                'Unable to locate cache file adaptor in config'
            );
        }

        $fileSystem = $options['fileSystem'];
        $fileName = $options['fileName'] ?? 'file_cache';
        $ttl = $options['ttl'] ?? null;

        /** @var FlySystemManager $manager */
        $manager = $this->getService($flyManagerServiceName);

        if (!$manager->has($fileSystem)) {
            throw new MissingServiceException(
                'Unable to locate file system: '.$fileSystem
            );
        }

        return new Adapter($manager->get($fileSystem), $fileName, $ttl);
    }
}
