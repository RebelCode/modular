<?php

namespace RebelCode\Modular\Events\UnitTest;

use stdClass;
use Xpmock\TestCase;
use RebelCode\Modular\Events\AbstractEventHandler as TestSubject;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AbstractEventHandlerTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Events\AbstractEventHandler';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return TestSubject|MockObject
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
                     ->_handle();

        return $mock->new();
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
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests the invocation of the handler.
     *
     * @since [*next-version*]
     */
    public function testInvoke()
    {
        $subject = $this->createInstance();

        $event = $this->getMockForAbstractClass('Psr\EventManager\EventInterface');

        $subject->expects($this->once())
                ->method('_handle')
                ->with($event);

        call_user_func_array($subject, [$event]);
    }

    /**
     * Tests the invocation of the handler with a non-event instance argument.
     *
     * @since [*next-version*]
     */
    public function testInvokeInvalidArg()
    {
        $subject = $this->createInstance();

        $arg = new stdClass();

        $subject->expects($this->never())
                ->method('_handle');

        $this->setExpectedException('InvalidArgumentException');

        call_user_func_array($subject, [$arg]);
    }
}
