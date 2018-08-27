<?php

namespace RebelCode\Modular\Config;

use Dhii\Config\ConfigInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Functionality for awareness of a configuration container.
 *
 * @since [*next-version*]
 */
trait ConfigAwareTrait
{
    /**
     * The configuration container.
     *
     * @var ConfigInterface
     */
    protected $config;

    /**
     * Retrieves the configuration associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return ConfigInterface The configuration container instance.
     */
    protected function _getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the configuration for this instance.
     *
     * @since [*next-version*]
     *
     * @param ConfigInterface $config The configuration container instance.
     *
     * @throws InvalidArgumentException If the configuration container is invalid.
     */
    protected function _setConfig($config)
    {
        if (!($config instanceof ConfigInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not a config instance'),
                null,
                null,
                $config
            );
        }

        $this->config = $config;
    }

    /**
     * Creates a new Dhii invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     * @param mixed|null                            $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     * @see   _translate()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
