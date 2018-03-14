<?php

namespace RebelCode\Modular\FuncTest\Loader;

use Dhii\Modular\Module\ModuleInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Modular\Iterator\DependencyModuleIterator as TestSubject;
use Xpmock\TestCase;

/**
 * Tests the {@see RebelCode\Modular\Iterator\DependencyModuleIterator} class.
 *
 * @since [*next-version*]
 */
class DependencyModuleIteratorTest extends TestCase
{
    /**
     * The name of the module class or interface to use for testing.
     *
     * @since [*next-version*]
     */
    const MODULE_CLASSNAME = 'Dhii\\Modular\\Module\\ModuleInterface';

    /**
     * Create a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $modules The list of modules.
     *
     * @return TestSubject
     */
    public function createInstance(array $modules = [])
    {
        return new TestSubject($modules);
    }

    /**
     * Creates an instance of a module.
     *
     * @since [*next-version*]
     *
     * @param string $key  The module key.
     * @param array  $deps The keys of the dependency modules.
     *
     * @return ModuleInterface|MockObject
     */
    public function createModuleInstance($key, array $deps = [])
    {
        $mock = $this->mockClassAndInterfaces(
            'stdClass',
            [
                'Dhii\Modular\Module\ModuleInterface',
                'Dhii\Modular\Module\DependenciesAwareInterface',
            ]
        );

        $mock->method('getKey')->willReturn($key);
        $mock->method('getDependencies')->willReturn($deps);

        return $mock;
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

        return $this->getMock($paddingClassName);
    }

    /**
     * Tests the iteration.
     *
     * @since [*next-version*]
     */
    public function testIteration()
    {
        $instance = $this->createInstance(
            [
                $this->createModuleInstance('a', ['b']),
                $this->createModuleInstance('b', ['c', 'd']),
                $this->createModuleInstance('c'),
                $this->createModuleInstance('d', ['a', 'c']),
            ]
        );

        $result = array_keys(iterator_to_array($instance));
        $expected = ['c', 'd', 'b', 'a'];

        $this->assertEquals($expected, $result);
    }
}
