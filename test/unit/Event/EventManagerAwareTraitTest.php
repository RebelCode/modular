<?php

namespace RebelCode\Modular\Events\FuncTest;

use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Modular\Events\EventManagerAwareTrait as TestSubject;
use stdClass;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class EventManagerAwareTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Events\EventManagerAwareTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return MockObject|TestSubject
     */
    public function createInstance()
    {
        // Create mock
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods(['__', '_createInvalidArgumentException'])
                     ->getMockForTrait();

        $mock->method('__')->willReturnArgument(0);
        $mock->method('_createInvalidArgumentException')->willReturnCallback(
            function ($msg = '', $code = 0, $prev = null) {
                return new InvalidArgumentException($msg, $code, $prev);
            }
        );

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

        $this->assertInternalType(
            'object',
            $subject,
            'An instance of the test subject could not be created'
        );
    }

    /**
     * Tests the getter and setter methods to ensure correct assignment and retrieval.
     *
     * @since [*next-version*]
     */
    public function testGetSetEventManager()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input = $this->getMock('Psr\EventManager\EventManagerInterface');

        $reflect->_setEventManager($input);

        $this->assertSame($input, $reflect->_getEventManager(), 'Set and retrieved value are not the same.');
    }

    /**
     * Tests the getter and setter methods with a null value to assert whether it is correctly stored.
     *
     * @since [*next-version*]
     */
    public function testGetSetEventManagerNull()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input = null;

        $reflect->_setEventManager($input);

        $this->assertSame($input, $reflect->_getEventManager(), 'Set and retrieved value are not the same.');
    }

    /**
     * Tests the getter and setter methods with an invalid value to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testGetSetEventManagerInvalid()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input = new stdClass();

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_setEventManager($input);
    }
}
