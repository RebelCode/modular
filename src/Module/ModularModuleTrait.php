<?php

namespace RebelCode\Modular\Module;

use ArrayAccess;
use Dhii\Modular\Module\ModuleInterface;
use Psr\Container\ContainerInterface;
use stdClass;
use Traversable;

/**
 * Functionality for modules that load other modules.
 *
 * This implementation constructs a "master" composite container on {@see _setup()} that contains a child container
 * with all of the module instances as services. Each module is then {@see _setup()} and their provided containers, if
 * any, are attached to the master container, which is then returned.
 *
 * On {@see _run()}, every module's {@see run()} method is invoked with the given container.
 *
 * @since [*next-version*]
 */
trait ModularModuleTrait
{
    /**
     * The list of module instances, mapped by their keys.
     *
     * @since [*next-version*]
     *
     * @var ModuleInterface[]|Traversable
     */
    protected $modules;

    /**
     * Sets up the modules, constructing the master composite container.
     *
     * @since [*next-version*]
     *
     * @return ContainerInterface The created composite container.
     */
    protected function _setup()
    {
        $moduleInitContainer = $this->_getModuleInitContainer();
        $modules = $this->_getModules($moduleInitContainer);

        // Setup all modules and collect their containers
        $this->modules = [];
        $containers = [];
        foreach ($modules as $_module) {
            $_container = $_module->setup();

            if ($_container !== null) {
                $containers[] = $_container;
            }

            $this->modules[$_module->getKey()] = $_module;
        }

        // Prepend a container with all module instances
        $modulesContainer = $this->_createContainer($this->modules);
        array_unshift($containers, $modulesContainer);

        // Prepend the container that was used to initialize modules
        array_unshift($containers, $moduleInitContainer);

        // Construct the master container and return
        return $this->_createCompositeContainer($containers);
    }

    /**
     * Runs the modules.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface|null $c The DI container.
     */
    protected function _run(ContainerInterface $c = null)
    {
        foreach ($this->modules as $_module) {
            $_module->run($c);
        }
    }

    /**
     * Retrieves the module instances to load.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface|null $container The container to initialize containers with.
     *
     * @return ModuleInterface[]|Traversable A list of module instances.
     */
    abstract protected function _getModules(ContainerInterface $container = null);

    /**
     * Retrieves the container to use to initialize modules.
     *
     * @since [*next-version*]
     *
     * @return ContainerInterface|null The container instance, if any.
     */
    abstract protected function _getModuleInitContainer();

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
}
