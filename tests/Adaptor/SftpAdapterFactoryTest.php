<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Adaptor;

use Blazon\PSR11FlySystem\Adapter\SftpAdapterFactory;
use Blazon\PSR11FlySystem\Exception\MissingConfigException;
use League\Flysystem\PathPrefixer;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @covers \Blazon\PSR11FlySystem\Adapter\SftpAdapterFactory
 */
class SftpAdapterFactoryTest extends TestCase
{
    /** @var SftpAdapterFactory */
    protected $factory;

    protected function setUp(): void
    {
        $this->factory = new SftpAdapterFactory();
        $this->assertInstanceOf(SftpAdapterFactory::class, $this->factory);
    }

    public function testConstructor()
    {
    }

    public function testGetConnector()
    {
        $host = 'some-host';
        $username = 'some-user';
        $password = 'some-pass';
        $port = 2345;
        $privateKey = 'some-private-key';
        $passphrase = 'some-passphrase';
        $useAgent = true;
        $timeout = 123;
        $maxTries = 123546;
        $fingerprint = 'abcdiefghijklmnop';

        $options = [
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'port' => $port,
            'privateKey' => $privateKey,
            'passphrase' => $passphrase,
            'useAgent' => $useAgent,
            'timeout' => $timeout,
            'maxTries' => $maxTries,
            'hostFingerprint' => $fingerprint
        ];

        $result = $this->factory->getConnector($options);

        $this->assertInstanceOf(SftpConnectionProvider::class, $result);

        // Check host was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'host'
        );

        $check->setAccessible(true);
        $this->assertEquals($host, $check->getValue($result));

        // Check username was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'username'
        );

        $check->setAccessible(true);
        $this->assertEquals($username, $check->getValue($result));

        // Check password was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'password'
        );

        $check->setAccessible(true);
        $this->assertEquals($password, $check->getValue($result));

        // Check useAgent was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'useAgent'
        );

        $check->setAccessible(true);
        $this->assertEquals($useAgent, $check->getValue($result));

        // Check port was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'port'
        );

        $check->setAccessible(true);
        $this->assertEquals($port, $check->getValue($result));

        // Check timeout was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'timeout'
        );

        $check->setAccessible(true);
        $this->assertEquals($timeout, $check->getValue($result));

        // Check maxTries was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'maxTries'
        );

        $check->setAccessible(true);
        $this->assertEquals($maxTries, $check->getValue($result));

        // Check maxTries was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'hostFingerprint'
        );

        $check->setAccessible(true);
        $this->assertEquals($fingerprint, $check->getValue($result));
    }

    public function testGetConnectorWithDefaults()
    {
        $host = 'some-host';
        $username = 'some-user';
        $password = '';
        $port = 21;
        $privateKey = null;
        $passphrase = null;
        $useAgent = false;
        $timeout = 10;
        $maxTries = 4;
        $fingerprint = null;

        $options = [
            'host' => $host,
            'username' => $username,
        ];

        $result = $this->factory->getConnector($options);

        $this->assertInstanceOf(SftpConnectionProvider::class, $result);

        // Check host was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'host'
        );

        $check->setAccessible(true);
        $this->assertEquals($host, $check->getValue($result));

        // Check username was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'username'
        );

        $check->setAccessible(true);
        $this->assertEquals($username, $check->getValue($result));

        // Check password was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'password'
        );

        $check->setAccessible(true);
        $this->assertEquals($password, $check->getValue($result));

        // Check useAgent was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'useAgent'
        );

        $check->setAccessible(true);
        $this->assertEquals($useAgent, $check->getValue($result));

        // Check port was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'port'
        );

        $check->setAccessible(true);
        $this->assertEquals($port, $check->getValue($result));

        // Check timeout was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'timeout'
        );

        $check->setAccessible(true);
        $this->assertEquals($timeout, $check->getValue($result));

        // Check maxTries was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'maxTries'
        );

        $check->setAccessible(true);
        $this->assertEquals($maxTries, $check->getValue($result));

        // Check maxTries was set correctly
        $check = new ReflectionProperty(
            SftpConnectionProvider::class,
            'hostFingerprint'
        );

        $check->setAccessible(true);
        $this->assertEquals($fingerprint, $check->getValue($result));
    }

    public function testGetConnectorMissingHost()
    {
        $this->expectException(MissingConfigException::class);

        $host = null;
        $username = 'some-user';

        $options = [
            'host' => $host,
            'username' => $username,
        ];

        $this->factory->getConnector($options);
    }

    public function testGetConnectorMissingUsername()
    {
        $this->expectException(MissingConfigException::class);

        $host = 'some-host';
        $username = null;

        $options = [
            'host' => $host,
            'username' => $username,
        ];

        $this->factory->getConnector($options);
    }

    public function testInvokeWithDefaults()
    {
        $root = '';
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

        $options = [];

        $mockFactory = $this->getMockBuilder(SftpAdapterFactory::class)
            ->onlyMethods(['getConnector'])
            ->getMock();

        $mockConnector = $this->getMockBuilder(SftpConnectionProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockFactory->expects($this->once())
            ->method('getConnector')
            ->with($this->equalTo($options))
            ->willReturn($mockConnector);

        $result = $mockFactory->__invoke($options);

        $this->assertInstanceOf(SftpAdapter::class, $result);

        // Check the root was set correctly
        $prefixerCheck = new ReflectionProperty(
            SftpAdapter::class,
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

        // Check permissions were set correctly
        $visibilityCheck = new ReflectionProperty(
            SftpAdapter::class,
            'visibilityConverter'
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
}
