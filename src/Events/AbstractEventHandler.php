<?php

namespace RebelCode\Modular\Events;

use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Invocation\InvocableInterface;
use Psr\EventManager\EventInterface;

/**
 * Common functionality for event handlers.
 *
 * @since [*next-version*]
 */
abstract class AbstractEventHandler implements InvocableInterface
{
    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function __invoke()
    {
        $event = func_get_arg(0);

        if (!$event instanceof EventInterface) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not an event instance'), null, null, $event
            );
        }

        $this->handle($event);
    }

    /**
     * Handles the event.
     *
     * @since [*next-version*]
     *
     * @param EventInterface $event The event instance.
     */
    abstract protected function handle(EventInterface $event);
}
