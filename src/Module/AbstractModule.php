<?php

namespace RebelCode\Modular\Module;

use Dhii\Data\KeyAwareTrait;
use RebelCode\Modular\Config\ConfigAwareTrait;

/**
 * Common & basic functionality for modules.
 *
 * @since [*next-version*]
 */
abstract class AbstractModule
{
    use KeyAwareTrait;
    use ConfigAwareTrait;
    use DependenciesAwareTrait;

    /**
     * Loads the module.
     *
     * @since [*next-version*]
     */
    protected function _load()
    {
    }
}
