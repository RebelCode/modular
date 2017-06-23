<?php

namespace RebelCode\Modular\Module;

use RebelCode\Modular\Util\CallbackAwareTrait;

/**
 * Basic functionality for a module that.
 *
 * @since [*next-version*]
 */
abstract class AbstractCallbackModule extends AbstractModule
{
    use CallbackAwareTrait;

    /**
     * Invokes the load callback.
     *
     * @since [*next-version*]
     *
     * @return mixed The value returned by the load callback.
     */
    protected function _invokeCallback()
    {
        $callback = $this->_getCallback();

        if (is_null($callback)) {
            return;
        }

        return call_user_func_array($callback, $this->_getCallbackParams());
    }

    /**
     * Retrieves the parameters to pass to the load callback when invoked.
     *
     * @since [*next-version*]
     *
     * @return array An array of parameters.
     */
    protected function _getCallbackParams()
    {
        return array();
    }

    /**
     * Normalizes a callback if it is invalid.
     *
     * @since [*next-version*]
     *
     * @param callable $callback
     *
     * @return $this
     */
    protected function _normalizeCallback($callback)
    {
        if (is_null($callback)) {
            return function () {};
        }

        return $callback;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _load()
    {
        $this->_invokeCallback();
    }
}
