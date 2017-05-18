<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use League\Flysystem\Azure\AzureAdapter;
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use WShafer\PSR11FlySystem\FactoryInterface;

class AzureAdapterFactory implements FactoryInterface
{
    public function __invoke(array $options)
    {
        $accountName = $options['accountName'] ?? null;
        $apiKey = $options['apiKey'] ?? null;
        $container = $options['container'] ?? null;
        $prefix = $options['prefix'] ?? null;

        $endpoint = sprintf(
            'DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s',
            $accountName,
            $apiKey
        );

        $proxy = ServicesBuilder::getInstance()->createBlobService($endpoint);
        return new AzureAdapter($proxy, $container, $prefix);
    }
}
