<?php

namespace RebelCode\Modular\Factory;

use Dhii\Factory\FactoryInterface;
use Dhii\Modular\Factory\ModuleFactoryInterface;
use Exception;
use RebelCode\Modular\Module\ModuleInterface;

/**
 * Concrete implementation of a module factory.
 *
 * @since [*next-version*]
 */
class ModuleFactory extends AbstractDelegatingFactory implements ModuleFactoryInterface
{
    /**
     * The exception code for generic module factory exceptions.
     *
     * @since [*next-version*]
     */
    const MODULE_FACTORY_EXCEPTION_CODE = 1;

    /**
     * The exception code for exceptions thrown due to module creation failure.
     *
     * @since [*next-version*]
     */
    const COULD_NOT_MAKE_MODULE_EXCEPTION_CODE = 2;

    /**
     * The service ID of the module service in the generic factory.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $moduleServiceId;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param FactoryInterface $factory         The generic factory to delegate to.
     * @param string           $moduleServiceId The ID of the module service definition.
     */
    public function __construct(FactoryInterface $factory, $moduleServiceId)
    {
        $this->_setGenericFactory($factory)
            ->_setModuleServiceId($moduleServiceId);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return ModuleInterface
     */
    public function makeModule($config)
    {
        return $this->_makeModule($config);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getModuleServiceId($config = array())
    {
        return $this->moduleServiceId;
    }

    /**
     * Sets the module service ID.
     *
     * @since [*next-version*]
     *
     * @param string $moduleServiceId The module service ID.
     *
     * @return $this
     */
    protected function _setModuleServiceId($moduleServiceId)
    {
        $this->moduleServiceId = $moduleServiceId;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createModuleFactoryException($message, Exception $inner = null) {
        return new ModuleFactoryException(
            $message,
            static::MODULE_FACTORY_EXCEPTION_CODE,
            $inner,
            $this
        );
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createCouldNotMakeModuleException(
        $message,
        $moduleKey = null,
        $moduleConfig = null,
        Exception $inner = null
    ) {
        return new CouldNotMakeModuleException(
            $message,
            static::COULD_NOT_MAKE_MODULE_EXCEPTION_CODE,
            $inner,
            $this,
            $moduleKey,
            $moduleConfig
        );
    }
}
