<?php

namespace RebelCode\Modular\UnitTest\Module;

use PHPUnit_Framework_TestCase;
use RebelCode\Modular\Module\Module;

/**
 * Tests {@see RebelCode\Modular\Module\Module}.
 *
 * @since [*next-version*]
 */
class ModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests the constructor to assert whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testConstructor()
    {
        $instance = new Module(
            'key',
            array('dep1', 'dep2'),
            array('some' => 'config', 'foo' => array('bar', 'baz')),
            function() {}
        );

        $this->assertInstanceOf(
            'RebelCode\\Modular\\Module\\Module',
            $instance,
            'Subject is not a valid instance.'
        );

        $this->assertInstanceOf(
            'RebelCode\\Modular\\Module\\ModuleInterface',
            $instance,
            'Subject does not extend the expected ancestor.'
        );

        $this->assertInstanceOf(
            'Dhii\\Modular\\Module\\ModuleInterface',
            $instance,
            'Subject does not extend the expected ancestor.'
        );
    }

    /**
     * Tests the constructor with a single parameter to assert whether the values given are
     * correctly saved.
     *
     * @since [*next-version*]
     */
    public function testConstructorOneParam()
    {
        $instance = new Module(
            $key    = 'test-key'
        );

        $this->assertEquals($key,    $instance->getKey());
        $this->assertEquals(array(), $instance->getDependencies());
        $this->assertEquals(array(), $instance->getConfig());
    }

    /**
     * Tests the constructor with two parameters to assert whether the values given are
     * correctly saved.
     *
     * @since [*next-version*]
     */
    public function testConstructorTwoParams()
    {
        $instance = new Module(
            $key    = 'test-key',
            $deps   = array('dep-1', 'dep-2')
        );

        $this->assertEquals($key,    $instance->getKey());
        $this->assertEquals($deps,   $instance->getDependencies());
        $this->assertEquals(array(), $instance->getConfig());
    }

    /**
     * Tests the constructor with three parameters to assert whether the values given are
     * correctly saved.
     *
     * @since [*next-version*]
     */
    public function testConstructorThreeParams()
    {
        $instance = new Module(
            $key    = 'test-key',
            $deps   = array('dep-1', 'dep-2'),
            $config = array('foo' => 'bar', 'some' => array('config'))
        );

        $this->assertEquals($key,    $instance->getKey());
        $this->assertEquals($deps,   $instance->getDependencies());
        $this->assertEquals($config, $instance->getConfig());
    }

    /**
     * Tests the constructor with four parameters to assert whether the values given are
     * correctly saved.
     *
     * @since [*next-version*]
     */
    public function testConstructorFourParams()
    {
        $instance = new Module(
            $key    = 'test-key',
            $deps   = array('dep-1', 'dep-2'),
            $config = array('foo' => 'bar', 'some' => array('config')),
            function() {}
        );

        $this->assertEquals($key,    $instance->getKey());
        $this->assertEquals($deps,   $instance->getDependencies());
        $this->assertEquals($config, $instance->getConfig());
    }

    /**
     * Tests the module's load method.
     *
     * @since [*next-version*]
     */
    public function testLoad()
    {
        $called   = 0;
        $instance = new Module(
            $key    = 'test-key',
            $deps   = array('dep-1', 'dep-2'),
            $config = array('foo' => 'bar', 'some' => array('config')),
            $onLoad = function() use (&$called) {
                $called++;
            }
        );

        $instance->load();

        $this->assertEquals(
            1, $called,
            sprintf('Module load callback was expected to be called once. Called %d times.', $called)
        );
    }
}
