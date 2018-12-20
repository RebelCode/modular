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
 * This implementation is capable of loading services and config from files. The files are expected to be PHP files
 * that `return` the services and config arrays respectively. However, services MAY be either an array or a
 * {@link ServiceProviderInterface} instance, which allows extensions to be read from the file as well.
 *
 * @since [*next-version*]
 */
abstract class AbstractModule implements
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
     * The service factory definitions for this module.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $factories;

    /**
     * The service extension definitions for this module.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $extensions;

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
        $this->initModule($key, $dependencies);
        $this->config = ($configFile !== null)
            ? $this->loadPhpDataFile($configFile)
            : [];
        $services = ($servicesFile !== null)
            ? $this->loadPhpDataFile($servicesFile)
            : [];
        $this->factories = ($services instanceof ServiceProviderInterface)
            ? $services->getFactories()
            : $services;
        $this->extensions = ($services instanceof ServiceProviderInterface)
            ? $services->getExtensions()
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
        return $this->factories;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
}
