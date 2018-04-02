<?php

namespace RebelCode\Modular\Events;

/**
 * A trait that aggregates common functionality for working with events.
 *
 * @since [*next-version*]
 */
trait EventsFunctionalityTrait
{
    /*
     * Provides event triggering functionality.
     *
     * @since [*next-version*]
     */
    use TriggerCapableTrait;

    /*
     * Provides value filtering functionality.
     *
     * @since [*next-version*]
     */
    use FilterCapableTrait;

    /*
     * Provides event attaching functionality.
     *
     * @since [*next-version*]
     */
    use AttachCapableTrait;

    /*
     * Provides event creation functionality.
     *
     * @since [*next-version*]
     */
    use CreateEventCapableTrait;

    /*
     * Provides awareness of an event manager.
     *
     * @since [*next-version*]
     */
    use EventManagerAwareTrait;

    /*
     * Provides awareness of an event factory.
     *
     * @since [*next-version*]
     */
    use EventFactoryAwareTrait;
}
