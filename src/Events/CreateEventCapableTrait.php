<?php

namespace RebelCode\Modular\Events;

use Dhii\EventManager\Event;
use Dhii\Factory\Exception\CouldNotMakeExceptionInterface;
use Psr\EventManager\EventInterface;

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
     * @return EventInterface The created event instance.
     *
     * @throws CouldNotMakeExceptionInterface If failed to make the event instance.
     */
    protected function _createEvent($name, $data)
    {
        return new Event($name, $data);
    }
}
