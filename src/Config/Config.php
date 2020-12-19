<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Config;

use WShafer\PSR11FlySystem\Exception\InvalidConfigException;

class Config
{
    protected $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getType(): string
    {
        if (empty($this->config['type'])) {
            throw new InvalidConfigException(
                "No type found in config for adaptor"
            );
        }

        return $this->config['type'];
    }

    public function getOptions(): array
    {
        return $this->config['type'] ?? [];
    }
}
