<?php

namespace RebelCode\Modular\Module;

use Dhii\Data\KeyAwareTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use stdClass;
use Traversable;

/**
 * Common functionality for modules.
 *
 * @since [*next-version*]
 */
trait ModuleTrait
{
    /* @since [*next-version*] */
    use KeyAwareTrait {
        _getKey as public getKey;
    }

    /* @since [*next-version*] */
    use DependenciesAwareTrait {
        _getDependencies as public getDependencies;
    }

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * Initializes the module.
     *
     * @since [*next-version*]
     *
     * @param string                     $key          The module key.
     * @param array|stdClass|Traversable $dependencies A list of keys for the modules that this module depends on.
     */
    protected function _initModule($key, $dependencies = [])
    {
        $this->_setKey($key);
        $this->_setDependencies($dependencies);
    }
}
