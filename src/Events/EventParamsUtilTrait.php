<?php

namespace RebelCode\Modular\Events;

use Psr\EventManager\EventInterface;

/**
 * Utility functionality for handling and changing event params.
 *
 * @since [*next-version*]
 */
trait EventParamsUtilTrait
{
    /**
     * Updates an event instance's params with a set of new values.
     *
     * * If a key in the given params already exists in the event, the value for that param is updated in the event.
     * * If a key in the given params does not exist in the event, the value for that param is added to the event.
     *
     * @since [*next-version*]
     *
     * @param EventInterface $event  The event instance.
     * @param array          $params The params to update.
     */
    protected function _updateEventParams(EventInterface $event, $params)
    {
        $event->setParams($params + $event->getParams());
    }

    /**
     * Sets the value for an event instance's param, adding the param to the event if it doesn't already exist.
     *
     * @since [*next-version*]
     *
     * @param EventInterface $event The event instance.
     * @param int|string     $key   The key of the param to set.
     * @param mixed          $value The value to set.
     */
    protected function _setEventParam(EventInterface $event, $key, $value)
    {
        $event->setParams([$key => $value] + $event->getParams());
    }
}
