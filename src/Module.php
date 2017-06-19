<?php
namespace WShafer\PSR11FlySystem;

class Module
{
    public function getConfig()
    {
        return [
            'service_manager' => [
                'factories' => [
                    \WShafer\PSR11FlySystem\FlySystemManager::class
                        =>\WShafer\PSR11FlySystem\FlySystemManagerFactory::class
                ]
            ]
        ];
    }
}
