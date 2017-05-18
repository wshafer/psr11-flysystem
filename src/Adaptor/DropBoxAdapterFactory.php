<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Adaptor;

use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;
use WShafer\PSR11FlySystem\FactoryInterface;

class DropBoxAdapterFactory implements FactoryInterface
{
    public function __invoke(array $options)
    {
        $token = $options['token'] ?? '';
        $prefix = $options['prefix'] ?? '';

        $client = new Client($token);
        return new DropboxAdapter($client, $prefix);
    }
}
