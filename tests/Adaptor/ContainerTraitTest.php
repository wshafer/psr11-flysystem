<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Test\Adaptor;

use Blazon\PSR11FlySystem\Adapter\ContainerTrait;
use Blazon\PSR11FlySystem\Adapter\S3AdapterFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Blazon\PSR11FlySystem\Adapter\ContainerTrait
 */
class ContainerTraitTest extends TestCase
{
    /** @var MockObject|S3AdapterFactory */
    protected $trait;

    /** @var MockObject|ContainerInterface */
    protected $mockContainer;

    /** @psalm-suppress all */
    protected function setUp(): void
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->trait = $this->getMockForTrait(ContainerTrait::class);

        $this->assertTrue(method_exists($this->trait, 'getContainer'));
        $this->assertTrue(method_exists($this->trait, 'setContainer'));
    }

    public function testConstructor()
    {
    }

    public function testGetAndSetContainer()
    {
        $this->trait->setContainer($this->mockContainer);
        $result = $this->trait->getContainer();
        $this->assertEquals($result, $this->mockContainer);
    }
}
