<?php

namespace RebelCode\Modular\Events;

use Dhii\Exception\CreateInternalExceptionCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;

/**
 * A trait for consumers to have all functionality required for working with events.
 *
 * @since [*next-version*]
 */
trait EventsConsumerTrait
{
    /*
     * Provides common functionality for working with events.
     *
     * @since [*next-version*]
     */
    use EventsFunctionalityTrait;

    /*
     * Provides functionality for creating invalid-argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /*
     * Provides functionality for creating runtime exceptions.
     *
     * @since [*next-version*]
     */
    use CreateRuntimeExceptionCapableTrait;

    /*
     * Provides functionality for creating internal exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInternalExceptionCapableTrait;

    /*
     * Provides string translating functionality.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;
}
