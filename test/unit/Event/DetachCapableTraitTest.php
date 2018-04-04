<?php

namespace RebelCode\Modular\Events\FuncTest;

use Psr\EventManager\EventManagerInterface;
use RebelCode\Modular\Events\DetachCapableTrait as TestSubject;
use RuntimeException;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class DetachCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Events\DetachCapableTrait';

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
                '_getEventManager',
                '_createRuntimeException',
                '__',
            ]
        );

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods($methods)
                     ->getMockForTrait();

        $mock->method('__')
             ->will($this->returnArgument(0));

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
     * Tests the `_detach()` method with an event name and event data to assert whether the handler is detached.
     *
     * @since [*next-version*]
     */
    public function testDetach()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $name = uniqid('event-');
        $handler = function() {};

        $manager = $this->createEventManager();
        $subject->expects($this->once())
                ->method('_getEventManager')
                ->willReturn($manager);

        $manager->expects($this->once())
                ->method('detach')
                ->with($name, $handler);

        $reflect->_detach($name, $handler);
    }

    /**
     * Tests the `_detach()` method to assert whether an exception is thrown when the event manager is null.
     *
     * @since [*next-version*]
     */
    public function testDetachNullEventManager()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $subject->expects($this->once())
                ->method('_getEventManager')
                ->willReturn(null);

        $subject->expects($this->once())
                ->method('_createRuntimeException')
                ->willReturn(new RuntimeException());

        $this->setExpectedException('RuntimeException');

        $reflect->_detach(uniqid(), []);
    }
}
