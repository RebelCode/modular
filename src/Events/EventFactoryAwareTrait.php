<?php

namespace RebelCode\Modular\Events;

use Dhii\Event\EventFactoryInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Common functionality for awareness of an event factory.
 *
 * @since [*next-version*]
 */
trait EventFactoryAwareTrait
{
    /**
     * The event factory.
     *
     * @since [*next-version*]
     *
     * @var EventFactoryInterface|null
     */
    protected $eventFactory;

    /**
     * Retrieves the event factory associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return EventFactoryInterface|null The event factory instance, if any.
     */
    protected function _getEventFactory()
    {
        return $this->eventFactory;
    }

    /**
     * Sets the event factory for this instance.
     *
     * @since [*next-version*]
     *
     * @param EventFactoryInterface|null $eventFactory The event factory instance or null.
     */
    protected function _setEventFactory($eventFactory)
    {
        if ($eventFactory !== null && !($eventFactory instanceof EventFactoryInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not an event factory instance'),
                null,
                null,
                $eventFactory
            );
        }

        $this->eventFactory = $eventFactory;
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
