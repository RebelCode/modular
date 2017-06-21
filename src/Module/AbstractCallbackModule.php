<?php

namespace RebelCode\Modular\Module;

use Dhii\Util\CallbackAwareTrait;

/**
 * Basic functionality for a module that
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
            return null;
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

    protected function _maybeSetCallback($callback)
    {
        if (!is_null($callback)) {
            $this->_setCallback($callback);
        }

        return $this;
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
