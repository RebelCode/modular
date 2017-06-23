<?php

namespace RebelCode\Modular\Factory;

use Dhii\Modular\Factory\ModuleFactoryExceptionInterface;
use Dhii\Modular\Factory\ModuleFactoryInterface;
use Exception;

/**
 * An exception that occurs in relation to a module factory.
 *
 * @since [*next-version*]
 */
class ModuleFactoryException extends Exception implements ModuleFactoryExceptionInterface
{
    /**
     * The module factory instance.
     *
     * @since [*next-version*]
     *
     * @var ModuleFactoryInterface
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param ModuleFactoryInterface $factory The module factory instance related to the exception.
     */
    public function __construct(
        $message = '',
        $code = 0,
        \Exception $previous = null,
        ModuleFactoryInterface $factory = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->_setModuleFactory($factory);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getModuleFactory()
    {
        return $this->factory;
    }

    /**
     * Sets the module factory instance.
     *
     * @since [*next-version*]
     *
     * @param ModuleFactoryInterface|null $factory The factory instance. Default: null
     *
     * @return $this
     */
    protected function _setModuleFactory(ModuleFactoryInterface $factory = null)
    {
        $this->factory = $factory;

        return $this;
    }
}
