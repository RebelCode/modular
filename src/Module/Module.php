<?php

namespace RebelCode\Modular\Module;

/**
 * Basic implementation of a module.
 *
 * @since [*next-version*]
 */
class Module extends AbstractCallbackModule implements ModuleInterface
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string   $key          The module key.
     * @param array    $dependencies The keys of dependency modules. Default: array()
     * @param array    $config       The configuration. Default: array()
     * @param callable $onLoad       The callback to invoke when the module is loaded. Default: null
     */
    public function __construct(
        $key,
        array $dependencies = array(),
        array $config = array(),
        callable $onLoad = null
    ) {
        $this->_setKey($key)
            ->_setDependencies($dependencies)
            ->_setConfig($config)
            ->_maybeSetCallback($onLoad);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getKey()
    {
        return $this->_getKey();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getDependencies()
    {
        return $this->_getDependencies();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getConfig()
    {
        return $this->_getConfig();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function load()
    {
        $this->_load();
    }
}
