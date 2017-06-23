<?php

namespace RebelCode\Modular\Config;

/**
 * Something that has configuration.
 *
 * @since [*next-version*]
 */
trait ConfigAwareTrait
{
    /**
     * The configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Retrieves the configuration.
     *
     * @since [*next-version*]
     *
     * @return array
     */
    protected function _getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the configuration.
     *
     * @since [*next-version*]
     *
     * @param array $config The configuration.
     *
     * @return $this
     */
    protected function _setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }
}
