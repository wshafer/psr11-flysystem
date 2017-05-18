<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Test\Adaptor;

use League\Flysystem\Azure\AzureAdapter;
use PHPUnit\Framework\TestCase;
use WShafer\PSR11FlySystem\Adaptor\AzureAdapterFactory;

/**
 * @covers \WShafer\PSR11FlySystem\Adaptor\AzureAdapterFactory
 */
class AzureAdaptorFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $azureAccountName = getenv('AZURE_ACCOUNT_NAME');
        $azureKey = getenv('AZURE_KEY');

        if (!$azureAccountName || !$azureKey) {
            $this->markTestSkipped('Missing needed keys to connect to azure.');
        }

        $factory = new AzureAdapterFactory();
        $class = $factory([
            'accountName' => $azureAccountName,
            'apiKey' => $azureKey,
            'container' => 'test_container',
            'prefix' => 'prefix_'
        ]);
        $this->assertInstanceOf(AzureAdapter::class, $class);
    }
}
