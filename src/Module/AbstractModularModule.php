<?php

namespace RebelCode\Modular\Module;

use ArrayAccess;
use Dhii\Collection\AddCapableInterface;
use Dhii\Config\ConfigInterface;
use Dhii\Modular\Module\ModuleInterface;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use RebelCode\Modular\Config\ConfigProviderInterface;
use stdClass;
use Traversable;

/**
 * Base functionality for modular modules.
 *
 * @since [*next-version*]
 */
abstract class AbstractModularModule implements
    /* @since [*next-version*] */
    ModuleInterface
{
    /* @since [*next-version*] */
    use ModuleTrait;

    /**
     * A list of the sub-modules.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $subModules;

    /**
     * The compiled service factories from all sub-modules.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $factories;

    /**
     * The compiled service extensions from all sub-modules.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $extensions;

    /**
     * A list of all the configs from all sub-modules.
     *
     * @since [*next-version*]
     *
     * @var array[]
     */
    protected $subConfigs;

    /**
     * A list of the sub-modules' setup() containers.
     *
     * @since [*next-version*]
     *
     * @var ContainerInterface[]
     */
    protected $subContainers;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string                                 $key          The module key.
     * @param array|stdClass|Traversable             $dependencies A list of keys for the modules that this module
     *                                                             depends on.
     * @param ModuleInterface[]|stdClass|Traversable $subModules   A list of sub-module instances.
     */
    public function __construct($key, $dependencies, $subModules)
    {
        $this->_setKey($key);
        $this->_setDependencies($dependencies);
        $this->_init($subModules);
    }

    /**
     * Initializes the module using a given list of sub-modules.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface[]|stdClass|Traversable $subModules The list sub-modules instances.
     */
    protected function _init($subModules)
    {
        $this->subModules    = [];
        $this->subConfigs    = [];
        $this->subContainers = [];
        $this->factories     = [];
        $this->extensions    = [];

        foreach ($subModules as $module) {
            $this->_addSubModule($module);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function setup()
    {
        $rootCntr = $this->_createCompositeContainer(
            $rootList = $this->_createAddCapableList()
        );

        $subModulesCntr = $this->_initSubModulesContainer($rootCntr);
        $configsCntr    = $this->_initConfigContainer($rootCntr);
        $servicesCntr   = $this->_initServicesContainer($rootCntr);
        $subSetupCntr   = $this->_initSubModulesSetupContainer();

        $rootList->add($subModulesCntr);
        $rootList->add($configsCntr);
        $rootList->add($servicesCntr);
        $rootList->add($subSetupCntr);

        return $rootCntr;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function run(ContainerInterface $c = null)
    {
        foreach ($this->subModules as $module) {
            $module->run($c);
        }
    }

    /**
     * Adds a sub-module to this module.
     *
     * This method MUST be called during the {@link _init()} process and SHOULD NOT have an effect thereafter.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $subModule The module instance to add as a sub-module.
     */
    protected function _addSubModule(ModuleInterface $subModule)
    {
        // Register the sub-container if the module's setup() returns one
        if ($container = $subModule->setup()) {
            $this->subContainers[] = $container;
        }

        // Register the sub-container's factories and extensions if it's a service provider
        if ($subModule instanceof ServiceProviderInterface) {
            $this->factories    = array_merge($this->factories, $subModule->getFactories());
            $this->extensions[] = $subModule->getExtensions();
        }

        // Register the sub-module's config if it's a config provider
        if ($subModule instanceof ConfigProviderInterface) {
            $this->subConfigs[] = $subModule->getConfig();
        }

        // Save the sub-module in a key->instance map
        $this->subModules[$this->_getModuleServiceKey($subModule)] = $subModule;
    }

    /**
     * Creates the container that holds the sub-module instances.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface|null $parent Optional parent container, used for resolving services in definitions.
     *
     * @return ContainerInterface The created container.
     */
    protected function _initSubModulesContainer(ContainerInterface $parent = null)
    {
        return $this->_createContainer($this->subModules, $parent);
    }

    /**
     * Creates the container that holds the config.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface|null $parent Optional parent container, used for resolving services in definitions.
     *
     * @return ContainerInterface The created container.
     */
    protected function _initConfigContainer(ContainerInterface $parent = null)
    {
        $configList = $this->_createAddCapableList();
        $configCntr = $this->_createCompositeContainer($configList);

        foreach (array_reverse($this->subConfigs) as $config) {
            $configList->add($this->_createConfig($config));
        }

        return $this->_createContainer(['config' => $configCntr], $parent);
    }

    /**
     * Creates the container that holds all of the services from all sub-modules.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface|null $parent Optional parent container, used for resolving services in definitions.
     *
     * @return ContainerInterface The created container.
     */
    protected function _initServicesContainer(ContainerInterface $parent = null)
    {
        foreach ($this->extensions as $group) {
            foreach ($group as $key => $callable) {
                if (!array_key_exists($key, $this->factories)) {
                    continue;
                }

                $currFactory = $this->factories[$key];
                $newFactory  = function (ContainerInterface $c) use ($currFactory, $callable) {
                    $prev = call_user_func_array($currFactory, [$c]);
                    $new  = call_user_func_array($callable, [$c, $prev]);

                    return $new;
                };

                $this->factories[$key] = $newFactory;
            }
        }

        return $this->_createContainer($this->factories, $parent);
    }

    /**
     * Creates the container that holds all of the sub-modules' setup() containers.
     *
     * @since [*next-version*]
     *
     * @return ContainerInterface The created container.
     */
    protected function _initSubModulesSetupContainer()
    {
        $cntrList  = $this->_createAddCapableList();
        $container = $this->_createCompositeContainer($cntrList);

        foreach (array_reverse($this->subContainers) as $subContainer) {
            $cntrList->add($subContainer);
        }

        return $container;
    }

    /**
     * Retrieves the service key for the service that represents a module.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module instance.
     *
     * @return string The module service key.
     */
    protected function _getModuleServiceKey(ModuleInterface $module)
    {
        return sprintf('%s_module', $module->getKey());
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
     */
    abstract protected function _createContainer($definitions = [], ContainerInterface $parent = null);

    /**
     * Creates a composite container with the given children container instances.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface[]|Traversable $containers The children container instances.
     *
     * @return ContainerInterface The created composite container instance.
     */
    abstract protected function _createCompositeContainer($containers);

    /**
     * Creates a config instance with the given data.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|ArrayAccess|ContainerInterface $data The config data.
     *
     * @return ConfigInterface The created config instance.
     */
    abstract protected function _createConfig($data);

    /**
     * Creates a new, modifiable list.
     *
     * @since [*next-version*]
     *
     * @return AddCapableInterface|Traversable The traversable, modifiable list.
     */
    abstract protected function _createAddCapableList();
}
