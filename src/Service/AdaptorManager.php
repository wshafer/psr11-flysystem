<?php
declare(strict_types=1);

namespace WShafer\PSR11FlySystem\Service;

use League\Flysystem\AdapterInterface;
use Psr\Container\ContainerInterface;
use WShafer\PSR11FlySystem\Config\MainConfig;
use WShafer\PSR11FlySystem\Exception\UnknownAdaptorException;
use WShafer\PSR11FlySystem\MapperInterface;

class AdaptorManager implements ContainerInterface
{
    /** @var MainConfig */
    protected $config;

    /** @var MapperInterface */
    protected $adaptorMapper;

    /** @var AdapterInterface[] */
    protected $adaptors = [];

    /** @var ContainerInterface */
    protected $container;

    /**
     * Manager constructor.
     * @param MainConfig         $config
     * @param MapperInterface    $adaptorMapper
     * @param ContainerInterface $container
     */
    public function __construct(
        MainConfig $config,
        MapperInterface $adaptorMapper,
        ContainerInterface $container
    ) {
        $this->config = $config;
        $this->adaptorMapper = $adaptorMapper;
        $this->container = $container;
    }

    /**
     * @param string $id
     *
     * @return AdapterInterface
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new UnknownAdaptorException(
                'Unable to locate adaptor '.$id.'.  Please check your configuration.'
            );
        }

        if (!key_exists($id, $this->adaptors)) {
            $adaptorConfig = $this->config->getAdaptorConfig($id);
            $this->adaptors[$id] = $this->adaptorMapper->get(
                $adaptorConfig->getType(),
                $adaptorConfig->getOptions()
            );
        }

        return $this->adaptors[$id];
    }

    public function has($id)
    {
        return $this->config->hasAdaptorConfig($id);
    }

    public function getConfig()
    {
        return $this->config;
    }
}
