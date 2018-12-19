<?php

namespace RebelCode\Modular\Config;

/**
 * The interface for a config provider, which provides configuration entries.
 *
 * @since [*next-version*]
 */
interface ConfigProviderInterface
{
    /**
     * Returns a list of all configuration entries registered by this config provider.
     *
     * Each entry has two parts:
     * - the key, which is the string by which the value is referenced
     * - the value, which can be any scalar value or a sub-config
     *
     * @since [*next-version*]
     *
     * @return array
     */
    public function getConfig();
}
