<?php

namespace RebelCode\Modular\Module;

use ArrayAccess;
use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use stdClass;
use Traversable;

/**
 * Base functionality for modular modules.it
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseModularModule extends AbstractBaseModule
{
    /*
     * Provides modular functionality for modules.
     *
     * @since [*next-version*]
     */
    use ModularModuleTrait;

    /**
     * The factory to use for creating composite containers.
     *
     * @since [*next-version*]
     *
     * @var ContainerFactoryInterface
     */
    protected $compContainerFactory;

    /**
     * Initializes the modular module with all required information.
     *
     * @since [*next-version*]
     *
     * @param ContainerFactoryInterface                     $compContainerFactory The composite container factory.
     * @param ContainerFactoryInterface                     $containerFactory     The container factory.
     * @param string|Stringable                             $key                  The module key.
     * @param string[]|Stringable[]                         $dependencies         The module dependencies.
     * @param array|ArrayAccess|stdClass|ContainerInterface $config               The module config.
     */
    protected function _initModularModule(
        ContainerFactoryInterface $compContainerFactory,
        ContainerFactoryInterface $containerFactory,
        $key,
        $dependencies = [],
        $config = []
    ) {
        $this->_setCompositeContainerFactory($compContainerFactory);
        $this->_initModule($containerFactory, $key, $dependencies, $config);
    }

    /**
     * Retrieves the composite container factory associated with this module.
     *
     * @since [*next-version*]
     *
     * @return ContainerFactoryInterface The composite container factory instance.
     */
    protected function _getCompositeContainerFactory()
    {
        return $this->compContainerFactory;
    }

    /**
     * Sets the composite container factory for this module.
     *
     * @since [*next-version*]
     *
     * @param ContainerFactoryInterface $compContainerFactory The composite container factory instance.
     */
    protected function _setCompositeContainerFactory(ContainerFactoryInterface $compContainerFactory)
    {
        $this->compContainerFactory = $compContainerFactory;
    }

    /**
     * Creates a composite container with the given children container instances.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface[]|Traversable $containers The children container instances.
     *
     * @return ContainerInterface The created composite container instance.
     */
    protected function _createCompositeContainer($containers)
    {
        return $this->_getCompositeContainerFactory()->make(['containers' => $containers]);
    }
}
