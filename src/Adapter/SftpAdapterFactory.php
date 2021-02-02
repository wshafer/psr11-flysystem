<?php

declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Adapter;

use Blazon\PSR11FlySystem\Exception\MissingConfigException;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

class SftpAdapterFactory implements FactoryInterface
{
    public function __invoke(array $options): FilesystemAdapter
    {
        $root = $options['root'] ?? '';
        $connector = $this->getConnector($options);
        $permissions = PortableVisibilityConverter::fromArray($options['permissions'] ?? []);
        return new SftpAdapter(
            $connector,
            $root,
            $permissions
        );
    }

    public function getConnector(array $options): SftpConnectionProvider
    {
        if (empty($options['host'])) {
            throw new MissingConfigException(
                "Sftp config missing host"
            );
        }

        if (empty($options['username'])) {
            throw new MissingConfigException(
                "Sftp config missing username"
            );
        }

        $host = $options['host'] ?? null;
        $username = $options['username'] ?? null;
        $password = $options['password'] ?? '';
        $port = $options['port'] ?? 21;
        $privateKey = $options['privateKey'] ?? null;
        $passphrase = $options['passphrase'] ?? null;
        $useAgent = $options['useAgent'] ?? false;
        $timeout = $options['timeout'] ?? 10;
        $maxTries = $options['maxTries'] ?? 4;
        $hostFingerprint = $options['hostFingerprint'] ?? null;

        return new SftpConnectionProvider(
            $host,
            $username,
            $password,
            $privateKey,
            $passphrase,
            $port,
            $useAgent,
            $timeout,
            $maxTries,
            $hostFingerprint
        );
    }
}
