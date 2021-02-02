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
    - [Laminas Service Manager](#laminas-service-manager)
- [Frameworks](#frameworks)
    - [Mezzio](#mezzio)
    - [Laminas](#laminas)
    - [Symfony](#symfony)
    - [Slim](#slim)
- [Configuration](#configuration)
    - [Minimal Configuration](#minimal-configuration)
        - [Example](#minimal-example)
    - [Full Configuration](#full-configuration)
        - [Example](#full-example)
    - [Adaptors](#adaptors)
        - [Local](#local)
        - [FTP](#ftp)
        - [SFTP](#sftp)
        - [Memory](#memory)
        - [Zip Archive](#zip-archive)
        - [AWS S3](#aws-s3)
        - [AsyncAws S3 Adapter](#async-aws-s3-adapter)
        - [Google Cloud Storage](#google-cloud-storage)
- [Upgrades](#upgrades)    
    - [Version 2 to version 3](#version-2-to-version-3)


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
$fileSystem->write('test.txt', 'this is test');
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
$fileSystem->write('test1.txt', 'this is a test');
print $fileSystem->read('test1.txt');
```

## Laminas Service Manager

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
            'other' => [
                'type' => 'local',
                'options' => [
                    'root' => '/tmp/zend'
                ],
            ],
        ],
    ],
]);

/** @var \League\Flysystem\FilesystemInterface $fileSystem */
$fileSystem = $container->get('someOtherAdaptor');
$fileSystem->write('test1.txt', 'this is a test');
print $fileSystem->read('test1.txt');
```

# Frameworks
Any framework that use a PSR-11 should work fine.   Below are some specific framework examples to get you started

## Mezzio
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
            'someOtherAdaptor' => [\WShafer\PSR11FlySystem\FlySystemFactory::class, 'someOtherAdaptor'],
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
    ],
];
```

## Laminas
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
            'someOtherAdaptor' => [\WShafer\PSR11FlySystem\FlySystemFactory::class, 'someOtherAdaptor'],
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
    ],
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
    someOtherAdaptor:
        factory: ['WShafer\PSR11FlySystem\FlySystemFactory', __callStatic]
        class: 'League\Flysystem\FilesystemInterface'
        arguments: ['someOtherAdaptor', ['@service_container']]
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
        
        $fileSystem = $this->container->get('someOtherAdaptor');
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
        ],
    ],
];

$app = new \Slim\App($config);

// Wire up the factory
$container = $app->getContainer();

// FlySystem using the default keys.
$container['fileSystem'] = new \WShafer\PSR11FlySystem\FlySystemFactory();

// FlySystem using a different filesystem configuration
$container['someOtherAdaptor'] = function ($c) {
    return \WShafer\PSR11FlySystem\FlySystemFactory::someOtherAdaptor($c);
};


// Example usage
$app->get('/example', function (Request $request, Response $response) {
    
    /** @var \League\Flysystem\FilesystemInterface $fileSystem */
    $fileSystem = $this->get('fileSystem');
    $fileSystem->write('default.txt', 'Hi there');

    /** @var \League\Flysystem\FilesystemInterface $fileSystem */
    $fileSystem = $this->get('someOtherAdaptor');
    $fileSystem->write('other.txt', 'Hi there');
});

$app->run();
```

# Configuration

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
                'type' => 'local', # Adaptor name or pre-configured service from the container
                
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
Note: A "default" adaptor is required.

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
                'options' => [],  // Adaptor specific options.  See adaptors below
            ],
            
            // Mount Manager Config
            'manager' => [
                'type' => 'manager', // Adaptor name or pre-configured service from the container
                'options' => [
                    'fileSystems' => [
                        'default' => 'default', // Adaptor name from adaptor configuration
                        'adaptorTwo' => 'adaptorTwo', // Adaptor name from adaptor configuration
                    ],
                ],
            ],
        ],
    ],
];

```

### Adaptors
Example configs for supported adaptors

#### Local

```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'local',
                'options' => [
                    'root' => '/path/to/root', // Required : Path on local filesystem
                    'writeFlags' => LOCK_EX,   // Optional : PHP flags.  See: file_get_contents for more info
                    'linkBehavior' => \League\Flysystem\Local\LocalFilesystemAdapter::DISALLOW_LINKS, // Optional : Link behavior
                    
                    // Optional:  Optional set of permissions to set for files
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

FlySystem Docs: [Local Adaptor](https://flysystem.thephpleague.com/v2/docs/adapter/local/)

#### FTP

```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'ftp',
                'options' => [
                    'host' => 'ftp.example.com', // Required : Host
                    'username' => 'username',    // Required : Username
                    'password' => 'password',    // Required : Password
                    'root' => '/root/path/', // required
                
                    // optional config settings
                    'port' => 21,
                    'ssl' => false,
                    'timeout' => 90,
                    'utf8' => false,
                    'passive' => true,
                    'transferMode' => FTP_BINARY,
                    'systemType' => null, // 'windows' or 'unix'
                    'ignorePassiveAddress' => null, // true or false
                    'timestampsOnUnixListingsEnabled' => false, // true or false
                    'recurseManually' => true, // true
                ],
            ],
        ],
    ],
];
```

FlySystem Docs: [FTP](https://flysystem.thephpleague.com/v2/docs/adapter/ftp/)

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
                'type' => 'sftp',
                'options' => [
                    'host' => 'example.com',                              // Required : Host
                    'port' => 21,                                         // Optional : Port
                    'username' => 'username',                             // Required : Username
                    'password' => 'password',                             // Optional : Password
                    'privateKey' => 'path/to/or/contents/of/privatekey',  // Optional : Private SSH Key
                    'passphrase' => 'passphrase',                         // Optional : SSH Key Passphrase
                    'root' => '/path/to/root',                            // Required : Root Path
                    'timeout' => 10,                                      // Optional : Timeout
                    'useAgent' => false,                                  // Optional : Use Agent (default: false)
                    'hostFingerprint' => 'fingerprint',                   // Optional : Host Fingerprint
                    'maxTries' => 4,                                      // Optional : Max tries
                    
                    // Optional:  Optional set of permissions to set for files
                    'permissions' => [
                        'file' => [
                            'public' => 0644,
                            'private' => 0600,
                        ],
                        'dir' => [
                            'public' => 0755,
                            'private' => 0700,
                        ],   
                    ],
                ],
            ],
        ],
    ],
];
```

FlySystem Docs: [SFTP](https://flysystem.thephpleague.com/v2/docs/adapter/sftp/)

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
                'options' => [],  // No options available
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
                    'path' => '/some/path/to/file.zip' // Required : File name and path to use for zip file
                ],
            ],
        ],
    ],
];
```

FlySystem Docs: [Zip Archive](https://flysystem.thephpleague.com/adapter/zip-archive/)


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
                    'client'  => 'some-container-service', // Required if client options not provided : S3 client service name
                    'key'     => 'aws-key',         // Required if no client provided : Key
                    'secret'  => 'aws-secret',  // Required if no client provided : Secret
                    'region'  => 'us-east-1',   // Required if no client provided : Region
                    'bucket'  => 'bucket-name', // Required : Bucket Name
                    'prefix'  => 'some/prefix', // Optional : Prefix
                    'version' => 'latest',      // Optional : Api Version.  Default: 'latest'
                    'dirPermissions' => \League\Flysystem\Visibility::PUBLIC, // or ::PRIVATE (Optional)
                ],
            ],
        ],
    ],
];
```
FlySystem Docs: [Aws S3 Adapter - SDK V3](https://flysystem.thephpleague.com/v2/docs/adapter/aws-s3-v3/)


#### Async Aws S3 Adapter

**Install**
```bash
composer require async-aws/simple-s3
composer require league/flysystem-async-aws-s3
```

**Config**
```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'AsyncAwsS3',
                'options' => [
                    'client'  => 'some-container-service', // Required if client options not provided : S3 client service name
                    'key'     => 'aws-key',           // Required if no client provided : Key
                    'secret'  => 'aws-secret',   // Required if no client provided : Secret
                    'region'  => 'us-east-1',             // Required if no client provided : Region
                    'bucket'  => 'bucket-name',           // Required : Bucket Name
                    'prefix'  => 'some/prefix',           // Optional : Prefix
                    'dirPermissions' => \League\Flysystem\Visibility::PUBLIC, // or ::PRIVATE (Optional)
                ],
            ],
        ],
    ],
];
```
FlySystem Docs: [AsyncAws S3 Adapter](https://flysystem.thephpleague.com/v2/docs/adapter/async-aws-s3/)

#### Google Cloud Storage

**Install**
```bash
composer require league/flysystem-google-cloud-storage
```

**Config**
```php
<?php

return [
    'flysystem' => [
        'adaptors' => [
            'default' => [
                'type' => 'GoogleCloudStorage',
                'options' => [
                    'bucket'            => 'bucket name or service', // Required
                    'client'            => 'service name',           // Required if no clientOptions are provided
                    // Required if no client is provided
                    'clientOptions' => [
                        'keyFile'   => 'path-to-key-file.json',  // Required : Auth key file
                        'projectId' => 'myProject', // Optional
                    ],
                    
                    'prefix'            => 'some/prefix',           // Optional : Prefix
                    'defaultVisibility' => \League\Flysystem\Visibility::PUBLIC, // or ::PRIVATE (Optional)
                    
                    // Optional permissions/acl
                    'permissions' => [
                        'entity'     => 'allUsers',
                        'publicAcl'  => \League\Flysystem\GoogleCloudStorage\PortableVisibilityHandler::ACL_PUBLIC_READ,
                        'privateAcl' => \League\Flysystem\GoogleCloudStorage\PortableVisibilityHandler::ACL_PRIVATE,
                    ],
                ],
            ],
        ],
    ],
];
```
FlySystem Docs: [Google Cloud Storage Adapter](https://github.com/thephpleague/flysystem-google-cloud-storage)

### Upgrades

#### Version 2 to Version 3
Version 3 upgrades FlySystem to version 2.  FlySystem version 2 is a brand-new take
on the great FlySystem.  The library has been slim down and simplified.  This has
caused us to also take a new approach which introduces a number of breaking changes.

##### Backwards compatibility breaks
* The File Manager has been removed.  This is a relic of version 1 and was deprecated
  in version 2.  You will need to update your code if you are still using this in your
  code base.

* File Caching has been removed upstream and as a result has been removed from
  this library as well.
  
* FlySystem plugins have been removed upstream and are no longer supported.
  
* With the removal of caching and plugins the configuration for file systems
  has been simplified.  The "fileSystems" key has been removed and now only the 
  "adaptors" key remains.
  
* The "Mount Manager" can now be configured like any other adaptor.
  
* Adaptors removed upstream:
    * Null
    * Azure
    * Dropbox
