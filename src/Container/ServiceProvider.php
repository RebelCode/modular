<?php

namespace RebelCode\Modular\Container;

use Interop\Container\ServiceProviderInterface;

/**
 * A service provider implementation.
 *
 * This class only exists because there is no implementation of a service provider that simply accepts static
 * factories and extensions through the constructor at the time of writing.
 *
 * @since [*next-version*]
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * The service factories.
     *
     * @since [*next-version*]
     *
     * @var callable[]
     */
    protected $factories;

    /**
     * The service extensions.
     *
     * @since [*next-version*]
     *
     * @var callable[]
     */
    protected $extensions;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array $factories  The service factories.
     * @param array $extensions The service extensions.
     */
    public function __construct(array $factories, array $extensions)
    {
        $this->factories = $factories;
        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getFactories()
    {
        return $this->factories;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
}
