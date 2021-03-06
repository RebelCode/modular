<?php

namespace RebelCode\Modular\Module;

use ArrayAccess;
use Dhii\Collection\AddCapableInterface;
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
 * It is very important to use a composite container that can work with a {@see Traversable} list of containers that is
 * initially empty and populated later. This is the only sane remedy for the chicken-egg problem.
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
        // The list of containers that will be added to the composite container
        $containers = $this->_createAddCapableList();
        // The composite container - the container for this modular module
        $composite = $this->_createCompositeContainer($containers);

        // The initial container that will be used to initialize the modules
        $initContainer = $this->_getInitialContainer($composite);
        if ($initContainer !== null) {
            $containers->add($initContainer);
        }

        $this->modules = [];

        // Get the modules to set up
        $modules          = $this->_getModules($initContainer);
        $moduleContainers = [];
        foreach ($modules as $_module) {
            // Set up the module and collect its container
            if ($_container = $_module->setup()) {
                $moduleContainers[] = $_container;
            }

            // Save module instance
            $this->modules[$this->_getModuleServiceKey($_module)] = $_module;
        }

        // Create a container that has the module instances as services, and add it to the list
        $containers->add($this->_createContainer($this->modules, $composite));

        // Add the containers retrieved from the modules, to the list
        foreach (array_reverse($moduleContainers) as $_moduleContainer) {
            $containers->add($_moduleContainer);
        }

        // Construct the master container and return
        return $composite;
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
     * Retrieves the service key for the service that represents a module.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface $module The module instance.
     *
     * @return string The module service key.
     */
    abstract protected function _getModuleServiceKey(ModuleInterface $module);

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
     * @param ContainerInterface|null $parent The parent container, if any.
     *
     * @return ContainerInterface|null The container instance, if any.
     */
    abstract protected function _getInitialContainer(ContainerInterface $parent = null);

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
     * Creates a new, modifiable list.
     *
     * @since [*next-version*]
     *
     * @return AddCapableInterface|Traversable The traversable, modifiable list.
     */
    abstract protected function _createAddCapableList();
}
