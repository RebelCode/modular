<?php

namespace RebelCode\Modular\Module;

/**
 * Something that has module dependencies.
 *
 * @since [*next-version*]
 */
trait DependenciesAwareTrait
{
    /**
     * The keys of dependency modules.
     *
     * @since [*next-version*]
     *
     * @var string[]
     */
    protected $dependencies;

    /**
     * Retrieves the keys of dependency modules.
     *
     * @since [*next-version*]
     *
     * @return string[]
     */
    protected function _getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Sets the keys of dependency modules.
     *
     * @since [*next-version*]
     *
     * @param string[] $dependencies The
     *
     * @return $this
     */
    protected function _setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;

        return $this;
    }
}
