<?php
require 'vendor/autoload.php';

$serviceManager = new \Zend\ServiceManager\ServiceManager([
    'factories' => [
        'fileSystem' => \WShafer\PSR11FlySystem\FlySystemFactory::class,
        'other' => [\WShafer\PSR11FlySystem\FlySystemFactory::class, 'other'],
    ],
]);

$serviceManager->setService('config', [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'local',
                'options' => [
                    'root' => '/tmp/zend'
                ],
            ],
        ],

        'fileSystems' => [
            // Array Keys are the file systems identifiers
            'other' => [
                'adaptor' => 'default'
            ],
        ],
    ],
]);

$container = new \Xtreamwayz\Pimple\Container([
    'fileSystem' => new \WShafer\PSR11FlySystem\FlySystemFactory(),
    'other' => function($c) {
        return \WShafer\PSR11FlySystem\FlySystemFactory::other($c);
    },
    'config' => [
        'flysystem' => [
            'adaptors' => [
                'myFiles' => [
                    'type' => 'local',
                    'options' => [
                        'root' => '/tmp/pimple'
                    ],
                ],
            ],

            'fileSystems' => [
                // Array Keys are the file systems identifiers
                'default' => [
                    'adaptor' => 'myFiles', # Adaptor name from adaptor configuration
                ],

                'other' => [
                    'adaptor' => 'myFiles'
                ],
            ],
        ],
    ]
]);

/** @var \League\Flysystem\FilesystemInterface $fileSystem */
$fileSystem = $serviceManager->get('other');
$fileSystem->put('test1.txt', 'this is also test 2');
print $fileSystem->get('test1.txt')->read();