<?php

namespace RebelCode\Modular\Events\FuncTest;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Modular\Events\Event;
use RebelCode\Modular\Events\EventParamsUtilTrait as TestSubject;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class EventParamsUtilTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Events\EventParamsUtilTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock.
     *
     * @return TestSubject|MockObject The new instance.
     */
    public function createInstance($methods = [])
    {
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods($methods)
                     ->getMockForTrait();

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
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests the event param updating functionality.
     *
     * @since [*next-version*]
     */
    public function testUpdateEventParams()
    {
        $existing = [
            'a' => 1,
            'b' => 2,
        ];
        $update = [
            'b' => 3,
            'c' => 4,
        ];

        $expected = [
            'a' => 1,
            'b' => 3,
            'c' => 4,
        ];

        $event = new Event(uniqid('event'), $existing);

        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $reflect->_updateEventParams($event, $update);

        $this->assertEquals($expected, $event->getParams());
    }

    /**
     * Tests the event param setting functionality with an existing key and a new value.
     *
     * @since [*next-version*]
     */
    public function testSetEventParamsExisting()
    {
        $existing = [
            'a' => 1,
            'b' => 2,
        ];
        $setKey = 'a';
        $setVal = 5;

        $expected = [
            'a' => 5,
            'b' => 2,
        ];

        $event = new Event(uniqid('event'), $existing);

        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $reflect->_setEventParam($event, $setKey, $setVal);

        $this->assertEquals($expected, $event->getParams());
    }

    /**
     * Tests the event param setting functionality with a new key and value.
     *
     * @since [*next-version*]
     */
    public function testSetEventParamsNew()
    {
        $existing = [
            'a' => 1,
            'b' => 2,
        ];
        $setKey = 'c';
        $setVal = 3;

        $expected = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];

        $event = new Event(uniqid('event'), $existing);

        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $reflect->_setEventParam($event, $setKey, $setVal);

        $this->assertEquals($expected, $event->getParams());
    }
}
