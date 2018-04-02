<?php

namespace RebelCode\Modular\Events\FuncTest;

use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Modular\Events\EventFactory as TestSubject;
use stdClass;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class EventFactoryTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Events\EventFactory';

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
        $subject = new TestSubject();

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );

        $this->assertInstanceOf(
            'Dhii\EventManager\EventFactoryInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    public function testMake()
    {
        $subject = new TestSubject();
        $name = uniqid('name-');
        $params = [
            uniqid('param-'),
            uniqid('key-') => uniqid('val-'),
            rand(0, 100),
        ];
        $target = new stdClass();
        $propagation = false;

        $actual = $subject->make(
            [
                'name'        => $name,
                'params'      => $params,
                'target'      => $target,
                'propagation' => $propagation,
            ]
        );

        $this->assertInstanceOf(
            'Psr\EventManager\EventInterface',
            $actual,
            'Created event does not implement expected interface.'
        );

        $this->assertEquals($name, $actual->getName(), 'Event name is incorrect.');
        $this->assertEquals($params, $actual->getParams(), 'Event params are incorrect.');
        $this->assertSame($target, $actual->getTarget(), 'Event target is incorrect.');
        $this->assertEquals($propagation, $actual->isPropagationStopped(), 'Event propagation flag is incorrect.');
    }
}
