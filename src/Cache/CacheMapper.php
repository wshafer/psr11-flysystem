<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Cache;

use WShafer\PSR11FlySystem\MapperAbstract;

class CacheMapper extends MapperAbstract
{
    public function getFactoryClassName(string $type)
    {
        if (class_exists($type)) {
            return $type;
        }

        switch ($type) {
            case 'psr6':
                return Psr6CacheFactory::class;
            case 'memory':
                return MemoryCacheFactory::class;
            case 'predis':
                return PredisCacheFactory::class;
        }

        return null;
    }
}
