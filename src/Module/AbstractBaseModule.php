<?php

namespace RebelCode\Modular\Module;

use Dhii\Data\Container\NormalizeContainerCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Modular\Module\DependenciesAwareInterface;
use Dhii\Modular\Module\ModuleInterface;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;

/**
 * Common base functionality for modules.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseModule implements
    ModuleInterface,
    DependenciesAwareInterface
{
    /*
     * Provides common module functionality.
     *
     * @since [*next-version*]
     */
    use ModuleTrait {
        _getKey as public getKey;
        _getDependencies as public getDependencies;
    }

    /*
     * Provides string normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /*
     * Provides iterable normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeIterableCapableTrait;

    /*
     * Provides container normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeContainerCapableTrait;

    /*
     * Provides functionality for creating invalid-argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /*
     * Provides string translating functionality.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;
}
