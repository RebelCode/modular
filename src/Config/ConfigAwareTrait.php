<?php

namespace RebelCode\Modular\Config;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ArrayAccess;
use stdClass;

/**
 * Functionality for awareness of a configuration container.
 *
 * @since [*next-version*]
 */
trait ConfigAwareTrait
{
    /**
     * The configuration container.
     *
     * @var array|ArrayAccess|stdClass|ContainerInterface
     */
    protected $config;

    /**
     * Retrieves the configuration associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return array|ArrayAccess|stdClass|ContainerInterface
     */
    protected function _getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the configuration for this instance.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface $config The configuration container.
     *
     * @throws InvalidArgumentException If the configuration container is invalid.
     */
    protected function _setConfig($config)
    {
        $this->config = $this->_normalizeContainer($config);
    }

    /**
     * Normalizes a container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface $container The container to normalize.
     *
     * @throws InvalidArgumentException If the container is invalid.
     *
     * @return array|ArrayAccess|stdClass|ContainerInterface Something that can be used with
     *                                                       {@see ContainerGetCapableTrait#_containerGet()} or
     *                                                       {@see ContainerHasCapableTrait#_containerHas()}.
     */
    abstract protected function _normalizeContainer($container);
}
