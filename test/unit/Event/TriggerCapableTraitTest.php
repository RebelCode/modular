<?php

namespace RebelCode\Modular\Events\FuncTest;

use Dhii\Exception\InternalException;
use Dhii\Factory\Exception\CouldNotMakeExceptionInterface;
use Exception;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\EventManager\EventInterface;
use Psr\EventManager\EventManagerInterface;
use RebelCode\Modular\Events\TriggerCapableTrait as TestSubject;
use RuntimeException;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class TriggerCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Events\TriggerCapableTrait';

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
        $methods = $this->mergeValues(
            $methods,
            [
                'getEventManager',
                'createEvent',
                '_createRuntimeException',
                '_createInternalException',
                '__',
            ]
        );

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods($methods)
                     ->getMockForTrait();

        $mock->method('__')->willReturnArgument(0);

        return $mock;
    }

    /**
     * Creates a new mock event manager instance.
     *
     * @since [*next-version*]
     *
     * @return MockObject|EventManagerInterface
     */
    public function createEventManager()
    {
        return $this->getMock('Psr\EventManager\EventManagerInterface');
    }

    /**
     * Creates a new mock event instance.
     *
     * @since [*next-version*]
     *
     * @param string|null $name   The event name.
     * @param array|null  $params The event params.
     *
     * @return MockObject|EventInterface
     */
    public function createEvent($name = null, $params = null)
    {
        $mock = $this->getMock('Psr\EventManager\EventInterface');

        if ($name !== null) {
            $mock->method('getName')->willReturn($name);
        }

        if ($params !== null) {
            $mock->method('getParams')->willReturn($params);
        }

        return $mock;
    }

    /**
     * Merges the values of two arrays.
     *
     * The resulting product will be a numeric array where the values of both inputs are present, without duplicates.
     *
     * @since [*next-version*]
     *
     * @param array $destination The base array.
     * @param array $source      The array with more keys.
     *
     * @return array The array which contains unique values
     */
    public function mergeValues($destination, $source)
    {
        return array_keys(array_merge(array_flip($destination), array_flip($source)));
    }

    /**
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string   $className      Name of the class for the mock to extend.
     * @param string[] $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return MockObject The object that extends and implements the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf(
            'abstract class %1$s extends %2$s implements %3$s {}',
            [
                $paddingClassName,
                $className,
                implode(', ', $interfaceNames),
            ]
        );
        eval($definition);

        return $this->getMockForAbstractClass($paddingClassName);
    }

    /**
     * Creates a new exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return RootException|MockObject The new exception.
     */
    public function createException($message = '')
    {
        $mock = $this->getMockBuilder('Exception')
                     ->setConstructorArgs([$message])
                     ->getMock();

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
     * Tests the `trigger()` method with an event name and event data to assert whether an event instance is created
     * internally, dispatched via an event manager and then returned.
     *
     * @since [*next-version*]
     */
    public function testTrigger()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $name = uniqid('event-');
        $data = [];

        $manager = $this->createEventManager();
        $subject->expects($this->once())
                ->method('getEventManager')
                ->willReturn($manager);

        $event = $this->createEvent();
        $subject->expects($this->once())
                ->method('createEvent')
                ->with($name, $data)
                ->willReturn($event);

        $manager->expects($this->once())
                ->method('trigger')
                ->with($event);

        $return = $reflect->trigger($name, $data);

        $this->assertSame($event, $return, 'Return value is not the created event instance.');
    }

    /**
     * Tests the `trigger()` method to assert whether an exception is thrown when the event manager is null.
     *
     * @since [*next-version*]
     */
    public function testTriggerNullEventManager()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $subject->expects($this->once())
                ->method('getEventManager')
                ->willReturn(null);

        $subject->expects($this->once())
                ->method('_createRuntimeException')
                ->willReturn(new RuntimeException());

        $this->setExpectedException('RuntimeException');

        $reflect->trigger(uniqid(), []);
    }

    /**
     * Tests the `trigger()` method to assert whether an exception is thrown when an event instance could not be
     * created.
     *
     * @since [*next-version*]
     */
    public function testTriggerCouldNotMakeEvent()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $name = uniqid('event-');
        $data = [];

        $manager = $this->createEventManager();
        $subject->expects($this->once())
                ->method('getEventManager')
                ->willReturn($manager);

        /* @var $prevEx CouldNotMakeExceptionInterface */
        $prevEx = $this->mockClassAndInterfaces(
            'Exception',
            ['Dhii\Factory\Exception\CouldNotMakeExceptionInterface']
        );
        $subject->expects($this->once())
                ->method('createEvent')
                ->with($name, $data)
                ->willThrowException($prevEx);

        $subject->expects($this->once())
                ->method('_createInternalException')
                ->willReturn(new InternalException(null, null, $prevEx));

        $this->setExpectedException('Dhii\Exception\InternalExceptionInterface');

        $reflect->trigger($name, $data);
    }

    /**
     * Tests the `trigger()` method to assert whether an exception is thrown when an exception is thrown and caught
     * during event dispatch.
     *
     * @since [*next-version*]
     */
    public function testTriggerException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $name = uniqid('event-');
        $data = [];

        $manager = $this->createEventManager();
        $subject->expects($this->once())
                ->method('getEventManager')
                ->willReturn($manager);

        $event = $this->createEvent();
        $subject->expects($this->once())
                ->method('createEvent')
                ->with($name, $data)
                ->willReturn($event);

        $manager->expects($this->once())
                ->method('trigger')
                ->willThrowException($prevEx = new Exception());

        $subject->expects($this->once())
                ->method('_createInternalException')
                ->willReturn(new InternalException(null, null, $prevEx));

        $this->setExpectedException('Dhii\Exception\InternalExceptionInterface');

        $reflect->trigger($name, $data);
    }
}
