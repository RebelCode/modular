<?php

namespace RebelCode\Modular\FuncTest\Module;

use RebelCode\Modular\Module\AbstractCallbackModule;
use Xpmock\TestCase;

/**
 * Tests {@see RebelCode\Modular\Module\AbstractCallbackModule}.
 *
 * @since [*next-version*]
 */
class AbstractCallbackModuleTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\\Modular\\Module\\AbstractCallbackModule';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return AbstractCallbackModule
     */
    public function createInstance($loadCallbackParams = array())
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->_getCallbackParams(function() use ($loadCallbackParams) {
                return $loadCallbackParams;
            })
            ->new();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'Subject is not a valid instance.'
        );

        $this->assertInstanceOf(
            'RebelCode\\Modular\\Module\\AbstractModule',
            $subject,
            'Subject does not extended the expected ancestor.'
        );
    }

    /**
     * Tests the invocation of the load callback.
     *
     * @since [*next-version*]
     */
    public function testLoad()
    {
        $called    = 0;             // num times the load callback was called
        $params    = array(2, '5'); // parameters to pass to load callback
        $pRecieved = array();       // the parameters recieved by the load callback

        $subject   = $this->createInstance($params);
        $reflect   = $this->reflect($subject);

        $reflect->_setCallback(function() use (&$pRecieved, &$called) {
            $pRecieved = func_get_args();
            $called++;
        });

        // "Load" the module
        $reflect->_load();

        $this->assertEquals(1, $called, sprintf('Expected load callback to be called once. Called %d times.', $called));
        $this->assertEquals($params, $pRecieved, 'Callback did not recieve correct parameters.');
    }
}
