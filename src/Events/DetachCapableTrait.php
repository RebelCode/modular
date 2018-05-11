<?php

namespace RebelCode\Modular\Events;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Psr\EventManager\EventManagerInterface;
use RuntimeException;

/**
 * Common functionality for detaching event listeners.
 *
 * @since [*next-version*]
 */
trait DetachCapableTrait
{
    /**
     * Detaches an event listener.
     *
     * @since [*next-version*]
     *
     * @param string   $event    The event to detach to.
     * @param callable $callback The listener callback function.
     *
     * @throws RuntimeException If the internal event manager is null.
     *
     * @return bool True on success, false on failure
     */
    protected function _detach($event, $callback)
    {
        $eventManager = $this->_getEventManager();

        if ($eventManager === null) {
            throw $this->_createRuntimeException($this->__('Internal event manager is null'), null, null);
        }

        return $eventManager->detach($event, $callback);
    }

    /**
     * Retrieves the event manager associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return EventManagerInterface|null The event manager instance, if any.
     */
    abstract protected function _getEventManager();

    /**
     * Creates a new Runtime exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     *
     * @return RuntimeException The new exception.
     */
    abstract protected function _createRuntimeException($message = null, $code = null, $previous = null);

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
