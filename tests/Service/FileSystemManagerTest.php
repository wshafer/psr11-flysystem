<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Service;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Cached\CacheInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use League\Flysystem\PluginInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Config\FileSystemConfig;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\Exception\UnknownFileSystemException;
use WShafer\PSR11FlySystem\Exception\UnknownPluginException;
use WShafer\PSR11FlySystem\Service\FileSystemManager;
use WShafer\PSR11FlySystem\Test\Stub\PluginStub;

class FileSystemManagerTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|MainConfig */
    protected $mockConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    /** @var \PHPUnit_Framework_MockObject_MockObject|FileSystemConfig */
    protected $mockFileSystemConfig;

    /** @var \PHPUnit_Framework_MockObject_MockObject|AdapterInterface */
    protected $mockAdapter;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CacheInterface */
    protected $mockCache;

    /** @var \PHPUnit_Framework_MockObject_MockObject|PluginInterface */
    protected $mockPlugin;

    /** @var  FileSystemManager */
    protected $manager;

    public function setup()
    {
        $this->mockConfig = $this->getMockBuilder(MainConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockFileSystemConfig = $this->getMockBuilder(FileSystemConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockAdapter = $this->createMock(AdapterInterface::class);
        $this->mockCache = $this->createMock(CacheInterface::class);
        $this->mockPlugin = new PluginStub();

        $this->manager = new FileSystemManager(
            $this->mockConfig,
            $this->mockContainer,
            $this->mockContainer,
            $this->mockContainer
        );
        $this->assertInstanceOf(FileSystemManager::class, $this->manager);
    }

    public function testConstructor()
    {
    }

    public function testHasTrue()
    {
        $this->mockConfig->expects($this->once())
            ->method('hasFileSystemConfig')
            ->with('IDo')
            ->willReturn(true);

        $this->assertTrue($this->manager->has('IDo'));
    }

    public function testHasFalse()
    {
        $this->mockConfig->expects($this->once())
            ->method('hasFileSystemConfig')
            ->with('IDo')
            ->willReturn(false);

        $this->assertFalse($this->manager->has('IDo'));
    }

    public function testGetSingleFileSystem()
    {
        $this->mockConfig->expects($this->once())
            ->method('getFileSystemConfig')
            ->with('IDo')
            ->willReturn($this->mockFileSystemConfig);

        $this->mockFileSystemConfig->expects($this->once())
            ->method('isManager')
            ->willReturn(false);

        $this->mockFileSystemConfig->expects($this->once())
            ->method('getAdaptor')
            ->willReturn('adaptor');

        $this->mockFileSystemConfig->expects($this->once())
            ->method('getCache')
            ->willReturn('cache');

        $this->mockFileSystemConfig->expects($this->exactly(2))
            ->method('getPlugins')
            ->willReturn(['plugin']);

        $map = [
            ['adaptor', $this->mockAdapter],
            ['cache', $this->mockCache],
            ['plugin', $this->mockPlugin],
        ];

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->with('plugin')
            ->willReturn(true);

        $fileSystem = $this->manager->get('IDo');

        $this->assertInstanceOf(Filesystem::class, $fileSystem);
    }

    public function testGetNoPlugins()
    {
        $this->mockConfig->expects($this->once())
            ->method('getFileSystemConfig')
            ->with('IDo')
            ->willReturn($this->mockFileSystemConfig);

        $this->mockFileSystemConfig->expects($this->once())
            ->method('isManager')
            ->willReturn(false);

        $this->mockFileSystemConfig->expects($this->once())
            ->method('getAdaptor')
            ->willReturn('adaptor');

        $this->mockFileSystemConfig->expects($this->once())
            ->method('getCache')
            ->willReturn('cache');

        $this->mockFileSystemConfig->expects($this->once())
            ->method('getPlugins')
            ->willReturn([]);

        $map = [
            ['adaptor', $this->mockAdapter],
            ['cache', $this->mockCache]
        ];

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->never())
            ->method('has')
            ->with('plugin');

        $fileSystem = $this->manager->get('IDo');

        $this->assertInstanceOf(Filesystem::class, $fileSystem);
    }

    public function testGetWithPluginsNotInContainer()
    {
        $this->expectException(UnknownPluginException::class);

        $this->mockConfig->expects($this->once())
            ->method('getFileSystemConfig')
            ->with('IDo')
            ->willReturn($this->mockFileSystemConfig);

        $this->mockFileSystemConfig->expects($this->once())
            ->method('isManager')
            ->willReturn(false);

        $this->mockFileSystemConfig->expects($this->once())
            ->method('getAdaptor')
            ->willReturn('adaptor');

        $this->mockFileSystemConfig->expects($this->once())
            ->method('getCache')
            ->willReturn('cache');

        $this->mockFileSystemConfig->expects($this->exactly(2))
            ->method('getPlugins')
            ->willReturn(['plugin']);

        $map = [
            ['adaptor', $this->mockAdapter],
            ['cache', $this->mockCache]
        ];

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with('plugin')
            ->willReturn(false);

        $this->manager->get('IDo');
    }

    public function testGetReturnsExistingService()
    {
        // Only called on the first request
        $this->mockConfig->expects($this->once())
            ->method('getFileSystemConfig')
            ->with('IDo')
            ->willReturn($this->mockFileSystemConfig);

        // Only called on the first request
        $this->mockFileSystemConfig->expects($this->once())
            ->method('isManager')
            ->willReturn(false);

        // Only called on the first request
        $this->mockFileSystemConfig->expects($this->once())
            ->method('getAdaptor')
            ->willReturn('adaptor');

        // Only called on the first request
        $this->mockFileSystemConfig->expects($this->once())
            ->method('getCache')
            ->willReturn('cache');

        // Only called on the first requestd but is called twice
        $this->mockFileSystemConfig->expects($this->exactly(2))
            ->method('getPlugins')
            ->willReturn(['plugin']);

        $map = [
            ['adaptor', $this->mockAdapter],
            ['cache', $this->mockCache],
            ['plugin', $this->mockPlugin],
        ];

        // Only called on the first request
        $this->mockContainer->expects($this->exactly(3))
            ->method('get')
            ->will($this->returnValueMap($map));

        // Only called on the first request
        $this->mockContainer->expects($this->once())
            ->method('has')
            ->with('plugin')
            ->willReturn(true);

        // Call One
        $this->manager->get('IDo');

        // Additional call should return existing instance
        $fileSystem = $this->manager->get('IDo');

        $this->assertInstanceOf(Filesystem::class, $fileSystem);
    }

    public function testGetNotFoundException()
    {
        $this->expectException(UnknownFileSystemException::class);
        $this->manager->get('IDo');
    }

    // Manager Tests
    public function testGetManager()
    {
        $this->mockConfig->expects($this->once())
            ->method('getFileSystemConfig')
            ->with('IDo')
            ->willReturn($this->mockFileSystemConfig);

        $this->mockFileSystemConfig->expects($this->once())
            ->method('isManager')
            ->willReturn(true);

        $this->mockFileSystemConfig->expects($this->exactly(2))
            ->method('getPlugins')
            ->willReturn(['plugin']);

        $mockRealFileSystem = $this->getMockBuilder(FileSystemConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockRealFileSystem->expects($this->once())
            ->method('getAdaptor')
            ->willReturn('adaptor');

        $mockRealFileSystem->expects($this->once())
            ->method('getCache')
            ->willReturn('cache');

        $mockRealFileSystem->expects($this->exactly(1))
            ->method('getPlugins')
            ->willReturn([]);

        $this->mockFileSystemConfig->expects($this->once())
            ->method('getFileSystems')
            ->willReturn(['mock' => $mockRealFileSystem]);

        $map = [
            ['adaptor', $this->mockAdapter],
            ['cache', $this->mockCache],
            ['plugin', $this->mockPlugin],
        ];

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->with('plugin')
            ->willReturn(true);

        $fileSystem = $this->manager->get('IDo');

        $this->assertInstanceOf(MountManager::class, $fileSystem);
    }
}
