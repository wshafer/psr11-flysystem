<?php
declare(strict_types=1);

return [
    'dependencies' => [
        'factories'  => [
            \WShafer\PSR11FlySystem\Config\MainConfig::class
                => \WShafer\PSR11FlySystem\Config\MainConfigFactory::class,
            \WShafer\PSR11FlySystem\Adaptor\AdaptorMapper::class
                => \WShafer\PSR11FlySystem\Adaptor\AdaptorMapperFactory::class,
            \WShafer\PSR11FlySystem\Cache\CacheMapper::class
                => \WShafer\PSR11FlySystem\Cache\CacheMapperFactory::class,
            \WShafer\PSR11FlySystem\Service\AdaptorManager::class
                => \WShafer\PSR11FlySystem\Service\AdaptorManagerFactory::class,
            \WShafer\PSR11FlySystem\Service\CacheManager::class
                => \WShafer\PSR11FlySystem\Service\CacheManagerFactory::class,
            \WShafer\PSR11FlySystem\Service\FileSystemManager::class
                => \WShafer\PSR11FlySystem\Service\FileSystemManagerFactory::class
        ]
    ]
];
