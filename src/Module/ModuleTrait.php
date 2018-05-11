<?php

namespace RebelCode\Modular\Module;

use Dhii\Data\KeyAwareTrait;

/**
 * Common functionality for modules.
 *
 * @since [*next-version*]
 */
trait ModuleTrait
{
    /*
     * Provides awareness and storage functionality for a key.
     *
     * @since [*next-version*]
     */
    use KeyAwareTrait;

    /*
     * Provides awareness and storage functionality for a list of dependencies.
     *
     * @since [*next-version*]
     */
    use DependenciesAwareTrait;
}
