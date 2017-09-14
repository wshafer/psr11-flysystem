<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Cache;

use League\Flysystem\Cached\Storage\Adapter;
use WShafer\PSR11FlySystem\Exception\MissingConfigException;
use WShafer\PSR11FlySystem\Exception\MissingServiceException;
use WShafer\PSR11FlySystem\FlySystemFactory;
use WShafer\PSR11FlySystem\FlySystemManager;

class AdaptorCacheFactory extends ContainerAwareCacheAbstract
{
    public function __invoke(array $options)
    {
        if (empty($options['adaptor'])) {
            throw new MissingConfigException(
                'Unable to locate cache file adaptor in config'
            );
        }

        $adaptor = $options['adaptor'];
        $fileName = $options['fileName'] ?? 'file_cache';
        $ttl = $options['ttl'] ?? null;

        /** @var FlySystemManager $manager */
        $manager = FlySystemFactory::getFlySystemManager($this->getContainer());
        $adaptorManager = $manager->getAdaptorManager();

        if (!$adaptorManager->has($adaptor)) {
            throw new MissingServiceException(
                'Unable to locate file system: '.$adaptor
            );
        }

        return new Adapter($adaptorManager->get($adaptor), $fileName, $ttl);
    }
}
