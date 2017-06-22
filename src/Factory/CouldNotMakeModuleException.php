<?php

namespace RebelCode\Modular\Factory;

use Dhii\Modular\Factory\CouldNotMakeModuleExceptionInterface;
use Dhii\Modular\Factory\ModuleFactoryInterface;
use Exception;

/**
 * An exception which occurs if a module factory is unable to create a module.
 *
 * @since [*next-version*]
 */
class CouldNotMakeModuleException extends ModuleFactoryException implements CouldNotMakeModuleExceptionInterface
{
    /**
     * The module key.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $key;

    /**
     * The module configuration.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $key    The key of the module that could not be created.
     * @param array  $config The configuration of the module that could not be created.
     */
    public function __construct(
        $message = "",
        $code = 0,
        Exception $previous = null,
        ModuleFactoryInterface $factory = null,
        $key = '',
        array $config = array()
    ) {
        parent::__construct($message, $code, $previous, $factory);

        $this->_setModuleKey($key)
            ->_setModuleConfig($config);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getModuleKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getModuleConfig()
    {
        return $this->config;
    }

    /**
     * Sets the key of the module that could not be created.
     *
     * @since [*next-version*]
     *
     * @param string $key The key of the module.
     *
     * @return $this
     */
    protected function _setModuleKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Sets the module configuration that resulted in creation failure.
     *
     * @since [*next-version*]
     *
     * @param array $config An array of configuration data.
     *
     * @return $this
     */
    protected function _setModuleConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }
}
