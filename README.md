[![codecov](https://codecov.io/gh/wshafer/psr11-flysystem/branch/master/graph/badge.svg)](https://codecov.io/gh/wshafer/psr11-flysystem)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wshafer/psr11-flysystem/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wshafer/psr11-flysystem/?branch=master)
[![Build Status](https://travis-ci.org/wshafer/psr11-flysystem.svg?branch=master)](https://travis-ci.org/wshafer/psr11-flysystem)
# PSR-11 FlySystem

FlySystem Factories for PSR-11




# Configuration

```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            # Array Keys are the names used for the adaptor
            'adaptor_one' => [
                'type' => 'local', #A daptor name or pre-configured service from the container
                'options' => [], # Adaptor specific options.  See adaptors below
            ],
            
            'adaptor_two' => [
                'type' => 'null', #A daptor name or pre-configured service from the container
                'options' => [], # Adaptor specific options.  See adaptors below
            ],
        ],
        
        'caches' => [
            # Array Keys are the names used for the cache
            'my_cache_service' => [
                'type' => 'psr6',
                # Cache specific options.  See caches below
                'options' => [
                    'service' => 'my_psr6_service_from_container',
                    'key' => 'my_key_',
                    'ttl' => 3000
                ], 
            ],
            
        ],
        
        'fileSystems' => [
            # Array Keys are the file systems identifiers
            'local' => [
                'adaptor' => 'adaptor_one', # Adaptor name from adaptor configuration
                'cache' => 'PSR6\Cache\Service', # PSR-6 pre-configured service
                'plugins' => [] # User defined plugins to be injected into the file system
            ],
            
            # Mount Manager Config
            'manager' => [
                'adaptor' => 'manager',
                'fileSystems' => [
                    'local' => [
                        'adaptor' => 'adaptor_one', # Adaptor name from adaptor configuration
                        'cache' => 'PSR6\Cache\Service', # PSR-6 pre-configured service
                        'plugins' => [] # User defined plugins to be injected into the file system
                    ],
                    
                    'anotherFileSystem' => [
                        'adaptor' => 'adaptor_two', # Adaptor name from adaptor configuration
                        'cache' => 'PSR6\Cache\Service', # PSR-6 pre-configured service
                        'plugins' => [] # User defined plugins to be injected into the file system
                    ],
                ]
            ]
        ]
    ],
];

```

## Adaptors
Example configs for supported adaptors

### Null/Test

```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'local' => [
                'type' => 'null',
                'options' => [], #No options available
            ],
        ],
    ],
];
```
FlySystem Docs: [Null Adaptor](https://flysystem.thephpleague.com/adapter/null-test/)


### Local

```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'local' => [
                'type' => 'local',
                'options' => [
                    'root' => '/path/to/root', # Path on local filesystem
                    'writeFlags' => LOCK_EX, # PHP flags.  See: file_get_contents for more info
                    'linkBehavior' => League\Flysystem\Adapter\Local::DISALLOW_LINKS, #Link behavior
                    'permissions' => [
                        'file' => [
                            'public' => 0744,
                            'private' => 0700,
                        ],
                        'dir' => [
                            'public' => 0755,
                            'private' => 0700,
                        ]    
                    ]
                ],
            ],
        ],
    ],
];
```

FlySystem Docs: [Local Adaptor](https://flysystem.thephpleague.com/adapter/local/)


## Caches
Example configs for supported caches

### Memory/Test

```php
<?php

return [
    'flysystem' => [
        'caches' => [
            'local' => [
                'type' => 'memory',
                'options' => [], #No options available
            ],
        ],
    ],
];
```
FlySystem Docs: [Caching](https://flysystem.thephpleague.com/caching/)

### PSR-6

```php
<?php

return [
    'flysystem' => [
        'caches' => [
            'local' => [
                'type' => 'psr6',
                'options' => [
                    'service' => 'my_psr6_service_from_container', # Service to be used from the container
                    'key' => 'my_key_', # Cache Key
                    'ttl' => 3000 # Expires
                ],
            ],
        ],
    ],
];
```
FlySystem Docs: Unknown

