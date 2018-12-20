<?php

namespace RebelCode\Modular\Events;

use Dhii\Event\EventFactoryInterface;
use Dhii\Factory\Exception\CouldNotMakeExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Psr\EventManager\EventInterface;
use RuntimeException;

/**
 * Functionality for creating event instances.
 *
 * @since [*next-version*]
 */
trait CreateEventCapableTrait
{
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
    protected function createEvent($name, $data)
    {
        $factory = $this->getEventFactory();

        if ($factory === null) {
            throw $this->_createRuntimeException(
                $this->__('Event factory is null'),
                null,
                null
            );
        }

        return $factory->make(
            [
                'name'   => $name,
                'params' => $data,
            ]
        );
    }

    /**
     * Retrieves the event factory instance.
     *
     * @since [*next-version*]
     *
     * @return EventFactoryInterface|null The event factory instance, if any.
     */
    abstract protected function getEventFactory();

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
