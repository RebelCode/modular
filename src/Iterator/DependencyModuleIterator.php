<?php

namespace RebelCode\Modular\Iterator;

use Dhii\Modular\Module\ModuleInterface;
use RebelCode\Modular\Module\ModuleInterface as RcModuleInterface;
use Iterator;

/**
 *  A module iterator that handles module dependencies.
 *
 * @since [*next-version*]
 */
class DependencyModuleIterator extends AbstractDependencyModuleIterator implements Iterator
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array $modules An array of modules.
     */
    public function __construct($modules = array())
    {
        $this->items = $modules;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getModuleDependencies(ModuleInterface $module)
    {
        if (!$module instanceof RcModuleInterface) {
            return array();
        }

        $keys      = $module->getDependencies();
        $instances = array_map(function ($key) {
            return $this->_getModuleByKey($key);
        }, $keys);

        return array_combine($keys, $instances);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function rewind()
    {
        return $this->_rewind();
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
        return $this->_next();
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
}
