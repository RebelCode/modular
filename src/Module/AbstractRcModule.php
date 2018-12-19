<?php

namespace RebelCode\Modular\Module;

use Dhii\Modular\Module\DependenciesAwareInterface;
use Dhii\Modular\Module\ModuleInterface;
use Interop\Container\ServiceProviderInterface;
use RebelCode\Modular\Config\ConfigProviderInterface;
use RebelCode\Modular\Util\LoadPhpDataFileCapableTrait;
use stdClass;
use Traversable;

/**
 * Common base functionality for modules.
 *
 * @since [*next-version*]
 */
abstract class AbstractRcModule implements
    /* @since [*next-version*] */
    ModuleInterface,
    /* @since [*next-version*] */
    DependenciesAwareInterface,
    /* @since [*next-version*] */
    ServiceProviderInterface,
    /* @since [*next-version*] */
    ConfigProviderInterface
{
    /* @since [*next-version*] */
    use ModuleTrait;

    /* @since [*next-version*] */
    use LoadPhpDataFileCapableTrait;

    /**
     * The config data for this module.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $config;

    /**
     * The service definitions for this module.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $services;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string                     $key          The module key.
     * @param array|stdClass|Traversable $dependencies A list of keys for the modules that this module depends on.
     * @param string|null                $configFile   The path to the file that contains this module's config.
     * @param string|null                $servicesFile The path of the file that contains this module's services.
     */
    public function __construct($key, $dependencies = [], $configFile = null, $servicesFile = null)
    {
        $this->_initModule($key, $dependencies);

        $this->config = ($configFile !== null)
            ? $this->_loadPhpDataFile($configFile)
            : [];

        $this->services = ($servicesFile !== null)
            ? $this->_loadPhpDataFile($servicesFile)
            : [];
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function setup()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getFactories()
    {
        return $this->services;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getExtensions()
    {
        return [];
    }
}
