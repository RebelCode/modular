<?php

namespace RebelCode\Modular\Events;

use Dhii\Exception\InternalExceptionInterface;
use Dhii\Util\String\StringableInterface;
use Psr\EventManager\EventInterface;
use RuntimeException;

/**
 * Provides functionality for filtering values by dispatching events.
 *
 * @since [*next-version*]
 */
trait FilterCapableTrait
{
    /**
     * Filters a value by dispatching an event to allow listeners to modify it.
     *
     * @since [*next-version*]
     *
     * @param string|EventInterface      $event The event key or instance.
     * @param string|StringableInterface $key   The key of the value to filter.
     * @param mixed                      $value The value to filter.
     *
     * @return mixed The filtered value.
     */
    protected function filter($event, $key, $value)
    {
        return $this->trigger($event, [$key => $value])->getParam($key);
    }

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
    abstract protected function trigger($event, $data = []);
}
