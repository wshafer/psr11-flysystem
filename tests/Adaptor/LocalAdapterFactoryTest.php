<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Adaptor;

use Blazon\PSR11FlySystem\Adapter\LocalAdapterFactory;
use Blazon\PSR11FlySystem\Exception\MissingConfigException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\PathPrefixer;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @covers \Blazon\PSR11FlySystem\Adapter\LocalAdapterFactory
 */
class LocalAdapterFactoryTest extends TestCase
{
    /** @var LocalAdapterFactory */
    protected $factory;

    protected function setUp(): void
    {
        $this->factory = new LocalAdapterFactory();
        $this->assertInstanceOf(LocalAdapterFactory::class, $this->factory);
    }

    public function testConstructor()
    {
    }

    public function testInvoke()
    {
        $root = '/tmp/';
        $writeFlags = LOCK_UN;
        $linkBehavior = LocalFilesystemAdapter::SKIP_LINKS;
        $permissions = [
            'file' => [
                'public' => 0777,
                'private' => 0400
            ],
            'dir' => [
                'public' => 0777,
                'private' => 0400
            ]
        ];

        $options = [
            'root' => $root,
            'writeFlags' => $writeFlags,
            'linkBehavior' => $linkBehavior,
            'permissions' => $permissions
        ];

        $result = ($this->factory)($options);
        $this->assertInstanceOf(LocalFilesystemAdapter::class, $result);

        // Check the root was set correctly
        $prefixerCheck = new ReflectionProperty(
            LocalFilesystemAdapter::class,
            'prefixer'
        );

        $prefixerCheck->setAccessible(true);
        $prefixer = $prefixerCheck->getValue($result);
        $this->assertInstanceOf(PathPrefixer::class, $prefixer);

        $prefixCheck = new ReflectionProperty(
            PathPrefixer::class,
            'prefix'
        );

        $prefixCheck->setAccessible(true);
        $this->assertEquals($root, $prefixCheck->getValue($prefixer));

        // Check write flag was set correctly
        $writeFlagCheck = new ReflectionProperty(
            LocalFilesystemAdapter::class,
            'writeFlags'
        );

        $writeFlagCheck->setAccessible(true);
        $this->assertEquals($writeFlags, $writeFlagCheck->getValue($result));

        // Check write flag was set correctly
        $linkBehaviorCheck = new ReflectionProperty(
            LocalFilesystemAdapter::class,
            'linkHandling'
        );

        $linkBehaviorCheck->setAccessible(true);
        $this->assertEquals($linkBehavior, $linkBehaviorCheck->getValue($result));

        // Check permissions were set correctly
        $visibilityCheck = new ReflectionProperty(
            LocalFilesystemAdapter::class,
            'visibility'
        );

        $visibilityCheck->setAccessible(true);
        $permissionsConverter = $visibilityCheck->getValue($result);
        $this->assertInstanceOf(PortableVisibilityConverter::class, $permissionsConverter);

        $filePublicCheck = new ReflectionProperty(
            PortableVisibilityConverter::class,
            'filePublic'
        );

        $filePublicCheck->setAccessible(true);
        $this->assertEquals($permissions['file']['public'], $filePublicCheck->getValue($permissionsConverter));

        $filePrivateCheck = new ReflectionProperty(
            PortableVisibilityConverter::class,
            'filePrivate'
        );

        $filePrivateCheck->setAccessible(true);
        $this->assertEquals($permissions['file']['private'], $filePrivateCheck->getValue($permissionsConverter));

        $dirPublicCheck = new ReflectionProperty(
            PortableVisibilityConverter::class,
            'directoryPublic'
        );

        $dirPublicCheck->setAccessible(true);
        $this->assertEquals($permissions['dir']['public'], $dirPublicCheck->getValue($permissionsConverter));

        $dirPrivateCheck = new ReflectionProperty(
            PortableVisibilityConverter::class,
            'directoryPrivate'
        );

        $dirPrivateCheck->setAccessible(true);
        $this->assertEquals($permissions['dir']['private'], $dirPrivateCheck->getValue($permissionsConverter));
    }

    public function testInvokeDefaultValues()
    {
        $root = sys_get_temp_dir() . '/';
        $writeFlags = LOCK_EX;
        $linkBehavior = LocalFilesystemAdapter::DISALLOW_LINKS;
        $permissions = [
            'file' => [
                'public' => 0644,
                'private' => 0600
            ],
            'dir' => [
                'public' => 0755,
                'private' => 0700
            ]
        ];

        $options = [
            'root' => $root
        ];

        $result = ($this->factory)($options);
        $this->assertInstanceOf(LocalFilesystemAdapter::class, $result);

        // Check the root was set correctly
        $prefixerCheck = new ReflectionProperty(
            LocalFilesystemAdapter::class,
            'prefixer'
        );

        $prefixerCheck->setAccessible(true);
        $prefixer = $prefixerCheck->getValue($result);
        $this->assertInstanceOf(PathPrefixer::class, $prefixer);

        $prefixCheck = new ReflectionProperty(
            PathPrefixer::class,
            'prefix'
        );

        $prefixCheck->setAccessible(true);
        $this->assertEquals($root, $prefixCheck->getValue($prefixer));

        // Check write flag was set correctly
        $writeFlagCheck = new ReflectionProperty(
            LocalFilesystemAdapter::class,
            'writeFlags'
        );

        $writeFlagCheck->setAccessible(true);
        $this->assertEquals($writeFlags, $writeFlagCheck->getValue($result));

        // Check write flag was set correctly
        $linkBehaviorCheck = new ReflectionProperty(
            LocalFilesystemAdapter::class,
            'linkHandling'
        );

        $linkBehaviorCheck->setAccessible(true);
        $this->assertEquals($linkBehavior, $linkBehaviorCheck->getValue($result));

        // Check permissions were set correctly
        $visibilityCheck = new ReflectionProperty(
            LocalFilesystemAdapter::class,
            'visibility'
        );

        $visibilityCheck->setAccessible(true);
        $permissionsConverter = $visibilityCheck->getValue($result);
        $this->assertInstanceOf(PortableVisibilityConverter::class, $permissionsConverter);

        $filePublicCheck = new ReflectionProperty(
            PortableVisibilityConverter::class,
            'filePublic'
        );

        $filePublicCheck->setAccessible(true);
        $this->assertEquals($permissions['file']['public'], $filePublicCheck->getValue($permissionsConverter));

        $filePrivateCheck = new ReflectionProperty(
            PortableVisibilityConverter::class,
            'filePrivate'
        );

        $filePrivateCheck->setAccessible(true);
        $this->assertEquals($permissions['file']['private'], $filePrivateCheck->getValue($permissionsConverter));

        $dirPublicCheck = new ReflectionProperty(
            PortableVisibilityConverter::class,
            'directoryPublic'
        );

        $dirPublicCheck->setAccessible(true);
        $this->assertEquals($permissions['dir']['public'], $dirPublicCheck->getValue($permissionsConverter));

        $dirPrivateCheck = new ReflectionProperty(
            PortableVisibilityConverter::class,
            'directoryPrivate'
        );

        $dirPrivateCheck->setAccessible(true);
        $this->assertEquals($permissions['dir']['private'], $dirPrivateCheck->getValue($permissionsConverter));
    }

    public function testInvokeMissingRootPath()
    {
        $this->expectException(MissingConfigException::class);
        ($this->factory)([]);
    }
}
