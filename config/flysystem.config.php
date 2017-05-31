<?php
declare(strict_types=1);

return [
    'dependencies' => [
        'factories'  => [
            \WShafer\PSR11FlySystem\FlySystemManager::class
                => \WShafer\PSR11FlySystem\FlySystemManagerFactory::class
        ],
    ],
];
