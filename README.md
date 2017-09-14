[![codecov](https://codecov.io/gh/wshafer/psr11-flysystem/branch/master/graph/badge.svg)](https://codecov.io/gh/wshafer/psr11-flysystem)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wshafer/psr11-flysystem/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wshafer/psr11-flysystem/?branch=master)
[![Build Status](https://travis-ci.org/wshafer/psr11-flysystem.svg?branch=master)](https://travis-ci.org/wshafer/psr11-flysystem)
# PSR-11 FlySystem

[FlySystem](https://flysystem.thephpleague.com/) Factories for PSR-11

#### Table of Contents
- [Installation](#installation)
- [Usage](#usage)
- [Containers](#containers)
    - [Pimple](#pimple-example)
    - [Zend Service Manager](#zend-service-manager)
- [Frameworks](#frameworks)
    - [Zend Expressive](#zend-expressive)
    - [Zend Framework 3](#zend-framework-3)
    - [Symfony](#symfony)
    - [Slim](#slim)
- [Configuration](#configuration)
    - [Minimal Configuration](#minimal-configuration)
        - [Example](#minimal-example)
    - [Full Configuration](#full-configuration)
        - [Example](#full-example)
    - [File System](#file-system)
    - [Adaptors](#adaptors)
        - [Null / Test](#nulltest)
        - [Local](#local)
        - [FTP](#ftp)
        - [SFTP](#sftp)
        - [Memory](#memory)
        - [Zip Archive](#zip-archive)
        - [Azure](#azure)
        - [AWS S3](#aws-s3)
        - [DropBox](#dropbox)
    - [Caches](#caches)
        - [Memory / Test](#memorytest)
        - [Adaptor](#adaptor)
        - [PSR-6](#psr-6)
        - [Predis](#predis)
        - [Memcached](#memcached)
        - [Stash](#stash)
- [Upgrades](#upgrades)    
    - [Version 1 to version 2](#version-1-to-version-2)


# Installation

```bash
composer require wshafer/psr11-flysystem
```

# Usage

```php
<?php

// Get the FlySystem FileSystem
$fileSystem = $container->get('myFileSystemService');

// Write to file
$fileSystem->put('test.txt', 'this is test');
```

Additional info can be found in the [documentation](https://flysystem.thephpleague.com/)

# Containers
Any PSR-11 container wil work.  In order to do that you will need to add configuration
and register a new service that points to `WShafer\PSR11FlySystem\FlySystemFactory` 

Below are some specific container examples to get you started

## Pimple Example
```php
// Create Container
$container = new \Xtreamwayz\Pimple\Container([
    // FlySystem using the default keys.
    'fileSystem' => new \WShafer\PSR11FlySystem\FlySystemFactory(),
    
    // FlySystem using a different filesystem configuration
    'other' => function($c) {
        return \WShafer\PSR11FlySystem\FlySystemFactory::other($c);
    },
    
    // Config
    'config' => [
        'flysystem' => [
            'adaptors' => [
                // At the bare minimum you must include a default adaptor.
                'default' => [  
                    'type' => 'local',
                    'options' => [
                        'root' => '/tmp/pimple'
                    ],
                ],
                
                // Some other Adaptor.  Keys are the names for each adaptor
                'someOtherAdaptor' => [
                    'type' => 'local',
                    'options' => [
                        'root' => '/tmp/pimple'
                    ],
                ],
            ],
            
            'fileSystems' => [
                'other' => [
                    'adaptor' => 'someOtherAdaptor'
                ],
            ],
        ],
    ]
]);

/** @var \League\Flysystem\FilesystemInterface $fileSystem */
$fileSystem = $container->get('other');
$fileSystem->put('test1.txt', 'this is a test');
print $fileSystem->get('test1.txt')->read();
```

## Zend Service Manager

```php
// Create the container and define the services you'd like to use
$container = new \Zend\ServiceManager\ServiceManager([
    'factories' => [
        // FlySystem using the default keys.
        'fileSystem' => \WShafer\PSR11FlySystem\FlySystemFactory::class,
        
        // FlySystem using a different filesystem configuration
        'other' => [\WShafer\PSR11FlySystem\FlySystemFactory::class, 'other'],
    ],
]);

// Config
$container->setService('config', [
    'flysystem' => [
        'adaptors' => [
            // At the bare minimum you must include a default adaptor.
            'default' => [  
                'type' => 'local',
                'options' => [
                    'root' => '/tmp/zend'
                ],
            ],
            
            // Some other Adaptor.  Keys are the names for each adaptor
            'someOtherAdaptor' => [
                'type' => 'local',
                'options' => [
                    'root' => '/tmp/zend'
                ],
            ],
        ],
        
        'fileSystems' => [
            'other' => [
                'adaptor' => 'someOtherAdaptor'
            ],
        ],
    ],
]);

/** @var \League\Flysystem\FilesystemInterface $fileSystem */
$fileSystem = $container->get('other');
$fileSystem->put('test1.txt', 'this is a test');
print $fileSystem->get('test1.txt')->read();
```

# Frameworks
Any framework that use a PSR-11 should work fine.   Below are some specific framework examples to get you started

## Zend Expressive
You'll need to add configuration and register the services you'd like to use.  There are number of ways to do that
but the recommended way is to create a new config file `config/autoload/flySystem.global.php`

### Configuration
config/autoload/flySystem.global.php
```php
<?php
return [
    'dependencies' => [
        'factories' => [
            // FlySystem using the default keys.
            'fileSystem' => \WShafer\PSR11FlySystem\FlySystemFactory::class,
            
            // FlySystem using a different filesystem configuration
            'other' => [\WShafer\PSR11FlySystem\FlySystemFactory::class, 'other'],
        ],
    ],
    
    'flysystem' => [
        'adaptors' => [
            // At the bare minimum you must include a default adaptor.
            'default' => [  
                'type' => 'local',
                'options' => [
                    'root' => '/tmp/zend'
                ],
            ],
            
            // Some other Adaptor.  Keys are the names for each adaptor
            'someOtherAdaptor' => [
                'type' => 'local',
                'options' => [
                    'root' => '/tmp/zend'
                ],
            ],
        ],
        
        'fileSystems' => [
            'other' => [
                'adaptor' => 'someOtherAdaptor'
            ],
        ],
    ],
];
```

## Zend Framework 3
You'll need to add configuration and register the services you'd like to use.  There are number of ways to do that
but the recommended way is to create a new config file `config/autoload/flySystem.global.php`

### Configuration
config/autoload/flySystem.global.php
```php
<?php
return [
    'service_manager' => [
        'factories' => [
            // FlySystem using the default keys.
            'fileSystem' => \WShafer\PSR11FlySystem\FlySystemFactory::class,
            
            // FlySystem using a different filesystem configuration
            'other' => [\WShafer\PSR11FlySystem\FlySystemFactory::class, 'other'],
        ],
    ],
    
    'flysystem' => [
        'adaptors' => [
            // At the bare minimum you must include a default adaptor.
            'default' => [  
                'type' => 'local',
                'options' => [
                    'root' => '/tmp/zend'
                ],
            ],
            
            // Some other Adaptor.  Keys are the names for each adaptor
            'someOtherAdaptor' => [
                'type' => 'local',
                'options' => [
                    'root' => '/tmp/zend'
                ],
            ],
        ],
        
        'fileSystems' => [
            'other' => [
                'adaptor' => 'someOtherAdaptor'
            ],
        ],
    ],
];
```

### Module Config
If you're not using the [Zend Component Installer](https://github.com/zendframework/zend-component-installer) you will 
also need to register the Module.

config/modules.config.php (ZF 3 skeleton)
```php
<?php

return [
    // ... Previously registered modules here
    'WShafer\\PSR11FlySystem',
];
```

config/application.config.php (ZF 2 skeleton)
```php
<?php

return [
    'modules' => [
        // ... Previously registered modules here
        'WShafer\\PSR11FlySystem',
    ]
];
```


## Symfony
While there are other Symfony bundles out there, as of Symfony 3.3 the service container is now 
a PSR-11 compatible container.  The following config below will get these factories registered and working
in Symfony.

### Configuration
app/config/config.yml (or equivalent)
```yaml
parameters:
    flysystem:
        adaptors:
            # At the bare minimum you must include a default adaptor.
            default:
                type: local
                options:
                    root: /tmp/symfony
            
            # Some other Adaptor.  Keys are the names for each adaptor
            someOtherAdaptor:
                type: local
                options:
                    root: /tmp/symfony
            
        fileSystems:
            other:
                adaptor: someOtherAdaptor
```

### Container Service Config
app/config/services.yml
```yaml
services:
    # FlySystem using the default keys.
    fileSystem:
        factory: 'WShafer\PSR11FlySystem\FlySystemFactory:__invoke'
        class: 'League\Flysystem\FilesystemInterface'
        arguments: ['@service_container']
        public: true
    
    # FlySystem using a different filesystem configuration
    other:
        factory: ['WShafer\PSR11FlySystem\FlySystemFactory', __callStatic]
        class: 'League\Flysystem\FilesystemInterface'
        arguments: ['other', ['@service_container']]
        public: true
    
    WShafer\PSR11FlySystem\FlySystemFactory:
        class: 'WShafer\PSR11FlySystem\FlySystemFactory'
        public: true
```


### Example Usage
src/AppBundle/Controller/DefaultController.php

```php
<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $fileSystem = $this->container->get('fileSystem');
        $fileSystem->write('default.txt', 'Hi there');
        
        $fileSystem = $this->container->get('other');
        $fileSystem->write('other.txt', 'Hi there');
    }
}
```

## Slim

public/index.php
```php
<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

// Add Configuration
$config = [
    'settings' => [
        'flysystem' => [
            'adaptors' => [
                // At the bare minimum you must include a default adaptor.
                'default' => [
                    'type' => 'local',
                    'options' => [
                        'root' => '/tmp/slim'
                    ],
                ],

                // Some other Adaptor.  Keys are the names for each adaptor
                'someOtherAdaptor' => [
                    'type' => 'local',
                    'options' => [
                        'root' => '/tmp/slim'
                    ],
                ],
            ],

            'fileSystems' => [
                'other' => [
                    'adaptor' => 'someOtherAdaptor'
                ],
            ],
        ],
    ],
];

$app = new \Slim\App($config);

// Wire up the factory
$container = $app->getContainer();

// FlySystem using the default keys.
$container['fileSystem'] = new \WShafer\PSR11FlySystem\FlySystemFactory();

// FlySystem using a different filesystem configuration
$container['other'] = function ($c) {
    return \WShafer\PSR11FlySystem\FlySystemFactory::other($c);
};


// Example usage
$app->get('/example', function (Request $request, Response $response) {
    
    /** @var \League\Flysystem\FilesystemInterface $fileSystem */
    $fileSystem = $this->get('fileSystem');
    $fileSystem->put('default.txt', 'Hi there');

    /** @var \League\Flysystem\FilesystemInterface $fileSystem */
    $fileSystem = $this->get('other');
    $fileSystem->put('other.txt', 'Hi there');
});

$app->run();
```

# Configuration

Fly System uses three types of services that will each need to be configured for your application.
In addition you will need to create a named service that maps to the `\WShafer\PSR11FlySystem\FlySystemFactory`
based on the container you are using.

- Named Services : These are services names wired up to a factory.  The configuration will differ
based on the type of container / framework in use.

- [Adaptors](#adaptors) : These are the adaptors to to the actual file system.  This could be
an Azure container, S3, Local, Memory, etc.

- [Caches](#caches) : (Optional) Cache layer to optimize performance.  While this is optional, this package
will use a memory cache by default if none is provide.

- [File System](#file-system) :  This will configure the final FlySystem File System that
you will actually use to do the work.   This uses the previous two configurations to get
you a fully functioning File System to work with.   In addition you can also configure
a [Mount Manager](https://flysystem.thephpleague.com/mount-manager/) to wire up multiple
file systems that need to interact with one another.

## Minimal Configuration
A minimal configuration would consist of at least defining one service and the "default" adaptor.

### Minimal Example (using Zend Expressive for the example)
```php
<?php

return [
    'dependencies' => [
        'factories' => [
            // FlySystem using the default keys.
            'MyServiceName' => \WShafer\PSR11FlySystem\FlySystemFactory::class,
        ],
    ],
    
    'flysystem' => [
        'adaptors' => [
            // Array Keys are the names used for the adaptor
            'default' => [
                'type' => 'local', #A daptor name or pre-configured service from the container
                
                // Adaptor specific options.  See adaptors below
                'options' => [
                    'root' => '/path/to/root', // Path on local filesystem
                ],
            ],
        ],
    ],
];
```
Using this setup you will be using the "default" file system with the "default" adaptor.  In this
example we will be using the local file adaptor as the default.

## Full Configuration
Note: An "default" adaptor is required.

### Full Example
```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            // Array Keys are the names used for the adaptor.  Default entry required for adaptors
            'default' => [
                'type' => 'local', // Adaptor name or pre-configured service from the container
                
                // Adaptor specific options.  See adaptors below
                'options' => [
                    'root' => '/path/to/root', // Path on local filesystem
                ],
            ],
            
            'adaptorTwo' => [
                'type' => 'null', // Adaptor name or pre-configured service from the container
                'options' => [], // Adaptor specific options.  See adaptors below
            ],
        ],
        
        'caches' => [
            // Array Keys are the names used for the cache
            'cache_one' => [
                'type' => 'psr6',
                // Cache specific options.  See caches below
                'options' => [
                    'service' => 'my_psr6_service_from_container',
                    'key' => 'my_key_',
                    'ttl' => 3000
                ], 
            ],
            
            'cache_two' => [
                'type' => 'psr6',
                // Cache specific options.  See caches below
                'options' => [
                    'service' => 'my_psr6_service_from_container',
                    'key' => 'my_key_',
                    'ttl' => 3000
                ], 
            ],
            
        ],
        
        'fileSystems' => [
            // Array Keys are the file systems identifiers.
            //
            // Note: You can specify "default" here to overwrite the default settings for the
            // default file system
            'default' => [
                'adaptor' => 'default', // Adaptor name from adaptor configuration
                'cache' => 'PSR6\Cache\Service', // Cache name from adaptor configuration
                'plugins' => [] // User defined plugins to be injected into the file system
            ],
            
            // Mount Manager Config
            'manager' => [
                'adaptor' => 'manager',
                'fileSystems' => [
                    'local' => [
                        'adaptor' => 'default', // Adaptor name from adaptor configuration
                        'cache' => 'cache_one', // PSR-6 pre-configured service
                        'plugins' => [] // User defined plugins to be injected into the file system
                    ],
                    
                    'anotherFileSystem' => [
                        'adaptor' => 'adaptor_two', // Adaptor name from adaptor configuration
                        'cache' => 'cache_two', // PSR-6 pre-configured service
                        'plugins' => [] // User defined plugins to be injected into the file system
                    ],
                ],
            ],
        ],
    ],
];

```

### File System
```php
<?php

return [
    'flysystem' => [
        'fileSystems' => [
            // Array Keys are the file systems identifiers
            'local' => [
                'adaptor' => 'adaptor_one', // Adaptor name from adaptor configuration
                'cache' => 'PSR6\Cache\Service', // Cache name from adaptor configuration
                'plugins' => [] // User defined plugins to be injected into the file system
            ],
        ],
    ],
];
```

### Adaptors
Example configs for supported adaptors

#### Null/Test

```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'null',
                'options' => [], #No options available
            ],
        ],
    ],
];
```
FlySystem Docs: [Null Adaptor](https://flysystem.thephpleague.com/adapter/null-test/)

#### Local

```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'local',
                'options' => [
                    'root' => '/path/to/root', // Path on local filesystem
                    'writeFlags' => LOCK_EX, // PHP flags.  See: file_get_contents for more info
                    'linkBehavior' => League\Flysystem\Adapter\Local::DISALLOW_LINKS, #Link behavior
                    'permissions' => [
                        'file' => [
                            'public' => 0644,
                            'private' => 0600,
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

#### FTP

```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'ftp',
                'options' => [
                    'host' => 'ftp.example.com',
                    'username' => 'username',
                    'password' => 'password',
                
                    /** optional config settings */
                    'port' => 21,
                    'root' => '/path/to/root',
                    'passive' => true,
                    'ssl' => true,
                    'timeout' => 30,
                ],
            ],
        ],
    ],
];
```

FlySystem Docs: [FTP](https://flysystem.thephpleague.com/adapter/ftp/)

#### SFTP
**Install**
```bash
composer require league/flysystem-sftp
```

**Config**
```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'ftp',
                'options' => [
                    'host' => 'example.com',
                    'port' => 21,
                    'username' => 'username',
                    'password' => 'password',
                    'privateKey' => 'path/to/or/contents/of/privatekey',
                    'root' => '/path/to/root',
                    'timeout' => 10,
                ],
            ],
        ],
    ],
];
```

FlySystem Docs: [SFTP](https://flysystem.thephpleague.com/adapter/sftp/)

#### Memory

**Install**
```bash
composer require league/flysystem-memory
```

**Config**
```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'memory',
                'options' => [],
            ],
        ],
    ],
];
```

FlySystem Docs: [Memory](https://flysystem.thephpleague.com/adapter/memory/)

#### Zip Archive

**Install**
```bash
composer require league/flysystem-ziparchive
```

**Config**
```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'zip',
                'options' => [
                    'path' => '/some/path/to/file.zip'    
                ],
            ],
        ],
    ],
];
```

FlySystem Docs: [Zip Archive](https://flysystem.thephpleague.com/adapter/zip-archive/)

#### Azure

**Install**
```bash
composer require league/flysystem-azure
```

**Config**
```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'azure',
                'options' => [
                    'accountName' => 'account-name',
                    'apiKey' => 'api-key',
                    'container' => 'container-name',
                    'prefix' => 'prefix_',  // Optional
                ],
            ],
        ],
    ],
];
```
FlySystem Docs: [Azure Adaptor](https://flysystem.thephpleague.com/adapter/azure/)

#### AWS S3
_Note: AWS V2 is not supported in this package_

**Install**
```bash
composer require league/flysystem-aws-s3-v3
```

**Config**
```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 's3',
                'options' => [
                    'key' => 'aws-key',
                    'secret'  => 'aws-secret',
                    'region'  => 'us-east-1',
                    'bucket'  => 'bucket-name',
                    'prefix'  => 'some/prefix', #optional
                    'version' => 'latest' // Default: 'latest'
                ],
            ],
        ],
    ],
];
```
FlySystem Docs: [Aws S3 Adapter - SDK V3](https://flysystem.thephpleague.com/adapter/aws-s3-v3/)

#### DropBox

**Install**
```bash
composer require spatie/flysystem-dropbox
```

**Config**
```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'dropbox',
                'options' => [
                    'token'   => 'my-token',
                    'prefix'  => 'prefix', #optional
                ],
            ],
        ],
    ],
];
```
FlySystem Docs: [DropBox](https://flysystem.thephpleague.com/adapter/dropbox/)

### Caches
Example configs for supported caches

#### Memory/Test

```php
<?php

return [
    'flysystem' => [
        'caches' => [
            'default' => [
                'type' => 'memory',
                'options' => [], #No options available
            ],
        ],
    ],
];
```
FlySystem Docs: [Caching](https://flysystem.thephpleague.com/caching/)

#### Adaptor

This cache adaptor will use another adaptor to store the cache to file to.  It will pull this file system
from the existing manager.

```php
<?php

return [
    'flysystem' => [
        'caches' => [
            'default' => [
                'type' => 'adaptor',
                'options' => [
                    'managerServiceName' => \WShafer\PSR11FlySystem\FlySystemManager::class, // Optional.  Only needed if you change the service name of the Fly Manager
                    'fileSystem' => 'MyFileSystemName', // Filesystem name found in the FileSystems config
                    'fileName' => 'my_cache_file', // File name for cache file
                    'ttl' => 300
                ], 
            ],
        ],
    ],
];
```
FlySystem Docs: [Caching](https://flysystem.thephpleague.com/caching/)

#### PSR-6

```php
<?php

return [
    'flysystem' => [
        'caches' => [
            'default' => [
                'type' => 'psr6',
                'options' => [
                    'service' => 'my_psr6_service_from_container', // Service to be used from the container
                    'key' => 'my_key_', // Cache Key
                    'ttl' => 3000 // Expires
                ],
            ],
        ],
    ],
];
```
FlySystem Docs: Unknown 

#### Predis

```php
<?php

return [
    'flysystem' => [
        'caches' => [
            'default' => [
                'type' => 'predis',
                'options' => [
                    'service' => 'my_predis_client_from_container', // Configured Predis Client Service to pull from container
                    'key' => 'my_key_', // Cache Key
                    'ttl' => 3000 // Expires
                ],
            ],
        ],
    ],
];
```
FlySystem Docs: [Caching](https://flysystem.thephpleague.com/caching/) 

#### Memcached

```php
<?php

return [
    'flysystem' => [
        'caches' => [
            'default' => [
                'type' => 'memcached',
                'options' => [
                    'service' => 'my_memcached_client_from_container', // Configured Memcached Client Service to pull from container
                    'key' => 'my_key_', // Cache Key
                    'ttl' => 3000 // Expires
                ],
            ],
        ],
    ],
];
```
FlySystem Docs: [Caching](https://flysystem.thephpleague.com/caching/) 


#### Stash
See: [PSR6](#psr-6)

_Note: While "The League" provides a native cache client, Stash itself already
implements a PSR 6 interface.  It is recommended to use that instead._


### Upgrades

#### Version 1 to Version 2
When upgrading from version 1 to version 2, there shouldn't be any changes needed.   Please note
that using the FlySystemManager directly is no longer recommended.  Named services
should be used instead.  See above for more info.

##### Changes
* A "default" filesystem entry is added automatically to the config.  This default
file system requires you to either specify a configured adaptor for the filesystem
OR there must be a default adaptor configured in order to use it.  Most Version 1
users should not have an issue with this change and the factories should still function
normally.

* The default cache can now be overwritten and reconfigured.   Previously a
"memory" cache was always used when the file system didn't state the cache to use.
 