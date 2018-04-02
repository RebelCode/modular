<?php

namespace RebelCode\Modular\Events\UnitTest;

use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\EventManager\EventInterface;
use RebelCode\Modular\Events\CreateEventCapableTrait as TestSubject;
use RuntimeException;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class CreateEventCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Events\CreateEventCapableTrait';

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
                '_getEventFactory',
            ]
        );

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods($methods)
                     ->getMockForTrait();

        return $mock;
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
     * Tests the event creation functionality to assert whether the created event instance is correct.
     *
     * @since [*next-version*]
     */
    public function testCreateEvent()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $name = uniqid('event-');
        $data = [
            uniqid('param-'),
            uniqid('key-') => uniqid('value-'),
            rand(0, 100),
        ];

        $factory = $this->getMock('Dhii\EventManager\EventFactoryInterface');

        $subject->expects($this->once())
                ->method('_getEventFactory')
                ->willReturn($factory);

        $event = $this->createEvent($name, $data);
        $factory->expects($this->once())
                ->method('make')
                ->with(['name' => $name, 'params' => $data])
                ->willReturn($event);

        $actual = $reflect->_createEvent($name, $data);

        $this->assertInstanceOf(
            'Psr\EventManager\EventInterface',
            $actual,
            'Created event does not implement expected interface.'
        );
        $this->assertSame($event, $actual, 'Returned event is not the event instance returned by the factory.');
    }

    /**
     * Tests the event creation functionality to assert whether an exception is thrown when the event factory is null.
     *
     * @since [*next-version*]
     */
    public function testCreateEventNoEventFactory()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $name = uniqid('event-');
        $data = [
            uniqid('param-'),
            uniqid('key-') => uniqid('value-'),
            rand(0, 100),
        ];

        $subject->expects($this->once())
                ->method('_getEventFactory')
                ->willReturn(null);

        $subject->expects($this->once())
                ->method('_createRuntimeException')
                ->willReturn(new RuntimeException());

        $this->setExpectedException('RuntimeException');

        $reflect->_createEvent($name, $data);
    }
}
