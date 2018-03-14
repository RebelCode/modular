<?php

namespace RebelCode\Modular\Iterator;

use ArrayIterator;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Iterator\NormalizeIteratorCapableTrait;
use Dhii\Modular\Module\DependenciesAwareInterface;
use Dhii\Modular\Module\ModuleInterface;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Iterator;
use IteratorIterator;
use stdClass;
use Traversable;

/**
 *  A module iterator that handles module dependencies.
 *
 * @since [*next-version*]
 */
class DependencyModuleIterator extends AbstractDependencyModuleIterator implements Iterator
{
    /*
     * Provides string normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /*
     * Provides iterator normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeIteratorCapableTrait;

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
     * Provides string translating functionality.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /**
     * A map of the modules to iterate over, mapped by their keys.
     *
     * This map is used to efficiently search for module instances by their keys, when resolving module dependencies.
     *
     * @since [*next-version*]
     *
     * @var ModuleInterface[]
     */
    protected $moduleMap;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array $modules An array of modules.
     */
    public function __construct($modules = array())
    {
        $this->_setModules($modules);
        $this->_createModuleMap($modules);
    }

    /**
     * Creates the map of modules to be iterator over, mapped by their key.
     *
     * @since [*next-version*]
     *
     * @param ModuleInterface[]|stdClass|Traversable $modules The modules to iterate over.
     */
    protected function _createModuleMap($modules)
    {
        $this->moduleMap = array();

        foreach ($modules as $_module) {
            $this->moduleMap[$_module->getKey()] = $_module;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getModuleDependencies(ModuleInterface $module)
    {
        if (!($module instanceof DependenciesAwareInterface)) {
            return array();
        }

        $dependencies = array();

        foreach ($module->getDependencies() as $_dep) {
            $_depKey = $this->_normalizeString($_dep);

            if (isset($this->moduleMap[$_depKey])) {
                $dependencies[$_depKey] = $this->moduleMap[$_depKey];
            }
        }

        return $dependencies;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function rewind()
    {
        $this->_rewind();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function current()
    {
        return $this->_current();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function key()
    {
        return $this->_key();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function next()
    {
        $this->_next();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function valid()
    {
        return $this->_valid();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createArrayIterator(array $array)
    {
        return new ArrayIterator($array);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createTraversableIterator(Traversable $traversable)
    {
        return new IteratorIterator($traversable);
    }
}
