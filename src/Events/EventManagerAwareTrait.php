<?php

namespace RebelCode\Modular\Events;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;
use Psr\EventManager\EventManagerInterface;

/**
 * Provides awareness of an event manager with storage and retrieval functionality.
 *
 * @since [*next-version*]
 */
trait EventManagerAwareTrait
{
    /**
     * The event manager instance.
     *
     * @since [*next-version*]
     *
     * @var EventManagerInterface|null
     */
    protected $eventManager;

    /**
     * Retrieves the event manager associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return EventManagerInterface|null The event manager instance, if any.
     */
    protected function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Sets the event manager for this instance.
     *
     * @since [*next-version*]
     *
     * @param EventManagerInterface|null $eventManager The event manager instance.
     */
    protected function setEventManager($eventManager)
    {
        if ($eventManager !== null && !($eventManager instanceof EventManagerInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not an event manager'),
                null,
                null,
                $eventManager
            );
        }

        $this->eventManager = $eventManager;
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
     * @see sprintf()
     * @see _translate()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
