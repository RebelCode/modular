<?php

namespace RebelCode\Modular\Module;

use Dhii\Util\String\StringableInterface;
use InvalidArgumentException;
use stdClass;
use Traversable;

/**
 * Functionality for awareness of a list of dependencies.
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
     * @var string[]|StringableInterface[]|stdClass|Traversable
     */
    protected $dependencies;

    /**
     * Retrieves the keys of dependency modules.
     *
     * @since [*next-version*]
     *
     * @return string[]|StringableInterface[]|stdClass|Traversable
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
     * @param string[]|StringableInterface[]|Traversable $dependencies The
     */
    protected function _setDependencies($dependencies)
    {
        $this->dependencies = $this->_normalizeIterable($dependencies);
    }

    /**
     * Normalizes an iterable.
     *
     * Makes sure that the return value can be iterated over.
     *
     * @since [*next-version*]
     *
     * @param mixed $iterable The iterable to normalize.
     *
     * @throws InvalidArgumentException If the iterable could not be normalized.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);
}
