<?php

namespace RebelCode\Modular\Module;

use ArrayAccess;
use Dhii\Config\ConfigFactoryInterface;
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
use RebelCode\Modular\Config\ConfigAwareTrait;
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
     * Provides awareness of module config.
     *
     * @since [*next-version*]
     */
    use ConfigAwareTrait;

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
     * The key in the module config where the module key can be found.
     *
     * @since [*next-version*]
     */
    const K_CONFIG_KEY = 'key';

    /**
     * The key in the module config where the module dependencies can be found.
     *
     * @since [*next-version*]
     */
    const K_CONFIG_DEPENDENCIES = 'dependencies';

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
     * @var ConfigFactoryInterface
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
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|ArrayAccess|ContainerInterface $config               The module config.
     * @param ConfigFactoryInterface                        $configFactory        The config factory.
     * @param ContainerFactoryInterface                     $containerFactory     The container factory.
     * @param ContainerFactoryInterface                     $compContainerFactory The composite container factory.
     * @param EventManagerInterface|null                    $eventManager         The event manager, or null.
     * @param EventFactoryInterface|null                    $eventFactory         The event factory, or null.
     */
    public function __construct(
        $config,
        ConfigFactoryInterface $configFactory,
        ContainerFactoryInterface $containerFactory,
        ContainerFactoryInterface $compContainerFactory,
        EventManagerInterface $eventManager = null,
        EventFactoryInterface $eventFactory = null
    ) {
        $this->_initModule($config, $configFactory, $containerFactory, $compContainerFactory);
        $this->_initModuleEvents($eventManager, $eventFactory);
    }

    /**
     * Initializes the module with all required information.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|ArrayAccess|ContainerInterface $config               The module config.
     * @param ConfigFactoryInterface                        $configFactory        The config factory.
     * @param ContainerFactoryInterface                     $containerFactory     The container factory.
     * @param ContainerFactoryInterface                     $compContainerFactory The composite container factory.
     */
    protected function _initModule(
        $config,
        ConfigFactoryInterface $configFactory,
        ContainerFactoryInterface $containerFactory,
        ContainerFactoryInterface $compContainerFactory
    ) {
        $this->_setKey($this->_containerGet($config, 'key'));
        $this->_setDependencies(
            $this->_containerHas($config, 'dependencies')
                ? $this->_containerGet($config, 'dependencies')
                : []
        );
        $this->_setConfig($config);
        $this->_setConfigFactory($configFactory);
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
     * Creates the module setup container for the module.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface $config   The config data.
     * @param array|ArrayAccess|stdClass|ContainerInterface $services The service definitions.
     *
     * @return ContainerInterface The created module setup container.
     */
    protected function _setupContainer($config, $services)
    {
        $configData        = $this->_createCompositeContainer([$this->_getConfig(), $config]);
        $configContainer   = $this->_createConfig($configData);
        $servicesContainer = ($services instanceof ContainerInterface) ? $services : $this->_createContainer($services);

        return $this->_createCompositeContainer([
            $configContainer,
            $servicesContainer,
        ]);
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
     * @return ConfigFactoryInterface The config container factory instance, if any.
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
     * @param ConfigFactoryInterface $configFactory The config container factory instance.
     */
    protected function _setConfigFactory($configFactory)
    {
        if ($configFactory !== null && !($configFactory instanceof ConfigFactoryInterface)) {
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
     * @throws CouldNotMakeExceptionInterface If the factory failed to create the exception.
     * @throws FactoryExceptionInterface      If the factory encountered an error.
     * @throws RuntimeException               If the container factory associated with this instance is null.
     *
     * @return ContainerInterface The created container instance.
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

        return $containerFactory->make([
            ContainerFactoryInterface::K_DATA => $definitions,
            'parent'                          => $parent,
        ]);
    }

    /**
     * Creates a config container instance with the given data.
     *
     * @since [*next-version*]
     *
     * @param callable[]|ArrayAccess|stdClass|ContainerInterface $data The config data.
     *
     * @throws CouldNotMakeExceptionInterface If the factory failed to create the exception.
     * @throws FactoryExceptionInterface      If the factory encountered an error.
     * @throws RuntimeException               If the container factory associated with this instance is null.
     *
     * @return ContainerInterface The created config container instance.
     */
    protected function _createConfig($data = [])
    {
        return $this->_getConfigFactory()->make([
            ConfigFactoryInterface::K_DATA => $data,
        ]);
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
