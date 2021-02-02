<?php


declare(strict_types=1);

namespace Blazon\PSR11FlySystem\Config;

use Blazon\PSR11FlySystem\Exception\InvalidConfigException;
use Blazon\PSR11FlySystem\Exception\MissingConfigException;

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
            throw new MissingConfigException(
                "No type found in config for adapter"
            );
        }

        return $this->config['type'];
    }

    public function getOptions(): array
    {
        return $this->config['options'] ?? [];
    }
}
