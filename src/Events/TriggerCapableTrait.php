<?php

namespace RebelCode\Modular\Events;

use Dhii\Exception\InternalException;
use Dhii\Exception\InternalExceptionInterface;
use Dhii\Factory\Exception\CouldNotMakeExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Psr\EventManager\EventInterface;
use Psr\EventManager\EventManagerInterface;
use RuntimeException;

/**
 * Functionality for triggering events.
 *
 * @since [*next-version*]
 */
trait TriggerCapableTrait
{
    /**
     * Triggers an event dispatch.
     *
     * @since [*next-version*]
     *
     * @param string|EventInterface $event The event key or instance.
     * @param array|object          $data  The data of the event, if any.
     *
     * @throws RuntimeException           If an error occurred and the event could not be dispatched.
     * @throws InternalExceptionInterface If an error occurred while dispatching the event.
     *
     * @return EventInterface The event instance.
     */
    protected function trigger($event, $data = [])
    {
        $eventManager = $this->getEventManager();

        if ($eventManager === null) {
            throw $this->_createRuntimeException($this->__('Internal event manager is null'), null, null);
        }

        try {
            $eventObj = ($event instanceof EventInterface)
                ? $event
                : $this->createEvent($event, $data);
        } catch (CouldNotMakeExceptionInterface $ex) {
            throw $this->_createInternalException($this->__('Failed to create event instance.'), null, $ex);
        }

        try {
            $eventManager->trigger($eventObj);
        } catch (RootException $ex) {
            throw $this->_createInternalException($this->__('An error occurred while triggering the event'), null, $ex);
        }

        return $eventObj;
    }

    /**
     * Retrieves the event manager associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return EventManagerInterface|null The event manager instance, if any.
     */
    abstract protected function getEventManager();

    /**
     * Creates a new event instance.
     *
     * @since [*next-version*]
     *
     * @param string       $name The event name.
     * @param array|object $data The event data.
     *
     * @throws CouldNotMakeExceptionInterface If failed to make the event instance.
     *
     * @return EventInterface The created event instance.
     */
    abstract protected function createEvent($name, $data);

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
     * Creates a new Internal exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     *
     * @return InternalException The new exception.
     */
    abstract protected function _createInternalException($message = null, $code = null, RootException $previous = null);

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
