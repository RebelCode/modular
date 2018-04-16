<?php

namespace RebelCode\Modular\Module;

use ArrayAccess;
use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\ContainerGetPathCapableTrait;
use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\CreateContainerExceptionCapableTrait;
use Dhii\Data\Container\CreateNotFoundExceptionCapableTrait;
use Dhii\Data\Container\NormalizeContainerCapableTrait;
use Dhii\Data\Container\NormalizeKeyCapableTrait;
use Dhii\Event\EventFactoryInterface;
use Dhii\Exception\CreateInternalExceptionCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\Exception\InternalException;
use Dhii\Factory\Exception\CouldNotMakeExceptionInterface;
use Dhii\Factory\Exception\FactoryExceptionInterface;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Modular\Module\DependenciesAwareInterface;
use Dhii\Modular\Module\ModuleInterface;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerInterface;
use Psr\EventManager\EventManagerInterface;
use RebelCode\Modular\Events\EventsFunctionalityTrait;
use RuntimeException;
use stdClass;
use Traversable;

/**
 * Common base functionality for modules.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseModule implements
    ModuleInterface,
    DependenciesAwareInterface
{
    /*
     * Provides common module functionality.
     *
     * @since [*next-version*]
     */
    use ModuleTrait {
        _getKey as public getKey;
        _getDependencies as public getDependencies;
    }

    /*
     * Provides functionality for retrieving a value for a path from any type of container hierarchy.
     *
     * @since [*next-version*]
     */
    use ContainerGetPathCapableTrait;

    /*
     * Provides functionality for reading from any type of container.
     *
     * @since [*next-version*]
     */
    use ContainerGetCapableTrait;

    /*
     * Provides functionality for key-checking any type of container.
     *
     * @since [*next-version*]
     */
    use ContainerHasCapableTrait;

    /*
     * Provides key normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeKeyCapableTrait;

    /*
     * Provides string normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /*
     * Provides iterable normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeIterableCapableTrait;

    /*
     * Provides container normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeContainerCapableTrait;

    /*
     * Provides common functionality for events.
     *
     * @since [*next-version*]
     */
    use EventsFunctionalityTrait;

    /*
     * Provides functionality for creating invalid-argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /*
     * Provides functionality for creating out-of-range exceptions.
     *
     * @since [*next-version*]
     */
    use CreateOutOfRangeExceptionCapableTrait;

    /*
     * Provides functionality for creating internal exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInternalExceptionCapableTrait;

    /*
     * Provides functionality for creating runtime exceptions.
     *
     * @since [*next-version*]
     */
    use CreateRuntimeExceptionCapableTrait;

    /*
     * Provides functionality for creating container exceptions.
     *
     * @since [*next-version*]
     */
    use CreateContainerExceptionCapableTrait;

    /*
     * Provides functionality for creating container not-found exceptions.
     *
     * @since [*next-version*]
     */
    use CreateNotFoundExceptionCapableTrait;

    /*
     * Provides string translating functionality.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /**
     * The factory to use for creating containers.
     *
     * @since [*next-version*]
     *
     * @var ContainerFactoryInterface
     */
    protected $containerFactory;

    /**
     * The factory to use for creating config containers.
     *
     * @since [*next-version*]
     *
     * @var ContainerFactoryInterface
     */
    protected $configFactory;

    /**
     * The factory to use for creating composite containers.
     *
     * @since [*next-version*]
     *
     * @var ContainerFactoryInterface
     */
    protected $compContainerFactory;

    /**
     * Initializes the module with all required information.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable         $key                  The module key.
     * @param string[]|Stringable[]     $dependencies         The module dependencies.
     * @param ContainerFactoryInterface $compContainerFactory The composite container factory.
     * @param ContainerFactoryInterface $containerFactory     The container factory.
     */
    protected function _initModule(
        $key,
        $dependencies = [],
        ContainerFactoryInterface $compContainerFactory,
        ContainerFactoryInterface $containerFactory
    ) {
        $this->_setKey($key);
        $this->_setDependencies($dependencies);
        $this->_setContainerFactory($containerFactory);
        $this->_setCompositeContainerFactory($compContainerFactory);
    }

    /**
     * Initializes the module's event functionality.
     *
     * @since [*next-version*]
     *
     * @param EventManagerInterface|null $eventManager The event manager, or null.
     * @param EventFactoryInterface|null $eventFactory The event factory, or null.
     */
    protected function _initModuleEvents($eventManager, $eventFactory)
    {
        $this->_setEventManager($eventManager);
        $this->_setEventFactory($eventFactory);
    }

    /**
     * Retrieves the container factory associated with this module.
     *
     * @since [*next-version*]
     *
     * @return ContainerFactoryInterface The container factory instance, if any.
     */
    protected function _getContainerFactory()
    {
        return $this->containerFactory;
    }

    /**
     * Sets the container factory for this module.
     *
     * @since [*next-version*]
     *
     * @param ContainerFactoryInterface $containerFactory The container factory instance or null.
     */
    protected function _setContainerFactory($containerFactory)
    {
        if ($containerFactory !== null && !($containerFactory instanceof ContainerFactoryInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not a container factory'),
                null,
                null,
                $containerFactory
            );
        }

        $this->containerFactory = $containerFactory;
    }

    /**
     * Retrieves the config container factory associated with this module.
     *
     * @since [*next-version*]
     *
     * @return ContainerFactoryInterface The config container factory instance, if any.
     */
    protected function _getConfigFactory()
    {
        return $this->configFactory;
    }

    /**
     * Sets the config container factory for this module.
     *
     * @since [*next-version*]
     *
     * @param ContainerFactoryInterface $configFactory The config container factory instance.
     */
    protected function _setConfigFactory($configFactory)
    {
        if ($configFactory !== null && !($configFactory instanceof ContainerFactoryInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not a container factory'),
                null,
                null,
                $configFactory
            );
        }

        $this->configFactory = $configFactory;
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
     * Creates a container instance with the given service definitions.
     *
     * @since [*next-version*]
     *
     * @param callable[]|ArrayAccess|stdClass|ContainerInterface $definitions The service definitions.
     * @param ContainerInterface|null                            $parent      The parent container instance, if any.
     *
     * @return ContainerInterface The created container instance.
     *
     * @throws CouldNotMakeExceptionInterface If the factory failed to create the exception.
     * @throws FactoryExceptionInterface      If the factory encountered an error.
     * @throws RuntimeException               If the container factory associated with this instance is null.
     */
    protected function _createContainer($definitions = [], ContainerInterface $parent = null)
    {
        $containerFactory = $this->_getContainerFactory();

        if (!($containerFactory instanceof ContainerFactoryInterface)) {
            throw $this->_createRuntimeException(
                $this->__('Not a valid container factory instance'),
                null,
                null
            );
        }

        return $containerFactory->make(['definitions' => $definitions, 'parent' => $parent]);
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

    /**
     * Loads a PHP config file and returns the configuration.
     *
     * Since module systems have varying loading mechanisms, it is not safe to assume that the current working directory
     * will be equivalent to the module's directory. Therefore, it is recommended to use absolute paths for the file
     * path argument.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $filePath The path to the PHP config file. Absolute paths are recommended.
     *
     * @throws InternalException   If an exception was thrown by the PHP config file.
     * @throws OutOfRangeException If the config retrieved from the PHP config file is not a valid container.
     * @throws RuntimeException    If the config file could not be read.
     *
     * @return array|ArrayAccess|stdClass|ContainerInterface The config.
     */
    protected function _loadPhpConfigFile($filePath)
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw $this->_createRuntimeException(
                $this->__('Config file does not exist or not readable'),
                null,
                null
            );
        }

        try {
            $config = require $filePath;
        } catch (Exception $exception) {
            throw $this->_createInternalException(
                $this->__('The PHP config file triggered an exception'),
                null,
                $exception
            );
        }

        try {
            return $this->_normalizeContainer($config);
        } catch (InvalidArgumentException $exception) {
            throw $this->_createOutOfRangeException(
                $this->__('The config retrieved from the PHP config file is not a valid container'),
                null,
                null,
                $config
            );
        }
    }
}
