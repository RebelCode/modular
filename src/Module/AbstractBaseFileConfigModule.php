<?php

namespace RebelCode\Modular\Module;

use Dhii\Exception\InternalExceptionInterface;

/**
 * Base functionality for a module that loads its config and services from files.
 *
 * This implementation expects the path to those files to be given in the module config at construction time, at keys:
 * * {@link static::K_CONFIG_CONFIG_FILE_PATH} for the config file path
 * * {@link static::K_CONFIG_SERVICES_FILE_PATH} for the services file path
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseFileConfigModule extends AbstractBaseModule
{
    /**
     * The key in the module config for the config file path.
     *
     * @since [*next-version*]
     */
    const K_CONFIG_CONFIG_FILE_PATH = 'config';

    /**
     * The key in the module config for the config file path.
     *
     * @since [*next-version*]
     */
    const K_CONFIG_SERVICES_FILE_PATH = 'services';

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws InternalExceptionInterface
     */
    public function setup()
    {
        $config = $this->_getConfig();

        return $this->_setupContainer(
            $this->_loadPhpConfigFile($config->get(static::K_CONFIG_CONFIG_FILE_PATH)),
            $this->_loadPhpConfigFile($config->get(static::K_CONFIG_SERVICES_FILE_PATH))
        );
    }
}
