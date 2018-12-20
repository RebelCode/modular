<?php

namespace RebelCode\Modular\Events\FuncTest;

use Dhii\Exception\InternalException;
use Psr\EventManager\EventInterface;
use RebelCode\Modular\Events\FilterCapableTrait as TestSubject;
use RuntimeException;
use stdClass;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class FilterCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Events\FilterCapableTrait';

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
                'trigger',
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
     * Tests the `filter()` method to assert whether an event is correctly dispatched and the correct value returned.
     *
     * @since [*next-version*]
     */
    public function testFilter()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $name = uniqid('name-');
        $key = uniqid('key-');
        $value = new stdClass();
        $newValue = new stdClass();

        $event = $this->createEvent($name, [$key => $newValue]);

        $subject->expects($this->once())
                ->method('trigger')
                ->with($name, [$key => $value])
                ->willReturn($event);

        $event->expects($this->once())
              ->method('getParam')
              ->with($key)
              ->willReturn($newValue);

        $actual = $reflect->filter($name, $key, $value);

        $this->assertSame($newValue, $actual, 'Returned value is not the new value in the event instance.');
    }

    /**
     * Tests the `filter()` method to assert whether runtime exceptions thrown within bubble out.
     *
     * @since [*next-version*]
     */
    public function testFilterRuntimeException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $name = uniqid('name-');
        $key = uniqid('key-');
        $value = new stdClass();

        $subject->expects($this->once())
                ->method('trigger')
                ->with($name, [$key => $value])
                ->willThrowException(new RuntimeException());

        $this->setExpectedException('RuntimeException');

        $reflect->filter($name, $key, $value);
    }

    /**
     * Tests the `filter()` method to assert whether internal exceptions thrown within bubble out.
     *
     * @since [*next-version*]
     */
    public function testFilterInternalException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $name = uniqid('name-');
        $key = uniqid('key-');
        $value = new stdClass();

        $subject->expects($this->once())
                ->method('trigger')
                ->with($name, [$key => $value])
                ->willThrowException(new InternalException(null, null, new RootException()));

        $this->setExpectedException('Dhii\Exception\InternalExceptionInterface');

        $reflect->filter($name, $key, $value);
    }
}
