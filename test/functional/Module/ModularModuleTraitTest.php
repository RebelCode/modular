<?php

namespace RebelCode\Modular\Module\FuncTest;

use Dhii\Modular\Module\ModuleInterface;
use Psr\Container\ContainerInterface;
use RebelCode\Modular\Module\ModularModuleTrait as TestSubject;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class ModularModuleTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Module\ModularModuleTrait';

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
                '_getModules',
                '_createContainer',
                '_createCompositeContainer',
            ]
        );

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods($methods)
                     ->getMockForTrait();

        return $mock;
    }

    /**
     * Creates a mock module instance.
     *
     * @since [*next-version*]
     *
     * @param string $key The module key.
     *
     * @return MockObject|ModuleInterface
     */
    public function createModule($key)
    {
        $mock = $this->getMockBuilder('Dhii\Modular\Module\ModuleInterface')
                     ->setMethods(
                         [
                             'getKey',
                             'setup',
                             'run',
                         ]
                     )
                     ->getMockForAbstractClass();

        $mock->method('getKey')->willReturn($key);

        return $mock;
    }

    /**
     * Creates a mock container instance.
     *
     * @since [*next-version*]
     *
     * @return MockObject|ContainerInterface
     */
    public function createContainer()
    {
        $mock = $this->getMockBuilder('Psr\Container\ContainerInterface')
                     ->setMethods(
                         [
                             'get',
                             'has',
                         ]
                     )
                     ->getMockForAbstractClass();

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
     * Tests the setup method to assert whether the modules are correctly set up and the composite container is
     * correctly constructed and returned.
     *
     * @since [*next-version*]
     */
    public function testSetup()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $modules = [
            $key1 = uniqid('key-') => $this->createModule($key1),
            $key2 = uniqid('key-') => $this->createModule($key2),
            $key3 = uniqid('key-') => $this->createModule($key3),
        ];

        $containers = [
            $key1 => $this->createContainer(),
            $key2 => $this->createContainer(),
            $key3 => $this->createContainer(),
        ];

        $subject->expects($this->once())
                ->method('_getModules')
                ->willReturn($modules);

        foreach ($modules as $_key => $_module) {
            $_module->expects($this->once())
                    ->method('setup')
                    ->willReturn($containers[$_key]);
        }

        $modulesContainer = $this->createContainer();
        $subject->expects($this->once())
                ->method('_createContainer')
                ->with($modules)
                ->willReturn($modulesContainer);

        $compositeContainer = $this->createContainer();
        $childrenContainers = [
            $modulesContainer,
            $containers[$key1],
            $containers[$key2],
            $containers[$key3],
        ];
        $subject->expects($this->once())
                ->method('_createCompositeContainer')
                ->with($childrenContainers)
                ->willReturn($compositeContainer);

        $this->assertSame(
            $compositeContainer,
            $reflect->_setup(),
            'Retrieved container instance is not the internally created composite container.'
        );
    }

    /**
     * Tests the setup method to assert whether modules can return `null` from `setup()`.
     *
     * @since [*next-version*]
     */
    public function testSetupSomeMissingContainers()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $modules = [
            $key1 = uniqid('key-') => $this->createModule($key1),
            $key2 = uniqid('key-') => $this->createModule($key2),
            $key3 = uniqid('key-') => $this->createModule($key3),
        ];

        $containers = [
            $key1 => $this->createContainer(),
            $key2 => null,
            $key3 => $this->createContainer(),
        ];

        $subject->expects($this->once())
                ->method('_getModules')
                ->willReturn($modules);

        foreach ($modules as $_key => $_module) {
            $_module->expects($this->once())
                    ->method('setup')
                    ->willReturn($containers[$_key]);
        }

        $modulesContainer = $this->createContainer();
        $subject->expects($this->once())
                ->method('_createContainer')
                ->with($modules)
                ->willReturn($modulesContainer);

        $compositeContainer = $this->createContainer();
        $childrenContainers = [
            $modulesContainer,
            $containers[$key1],
            $containers[$key3],
        ];
        $subject->expects($this->once())
                ->method('_createCompositeContainer')
                ->with($childrenContainers)
                ->willReturn($compositeContainer);

        $this->assertSame(
            $compositeContainer,
            $reflect->_setup(),
            'Retrieved container instance is not the internally created composite container.'
        );
    }

    /**
     * Tests the run method to assert whether all modules have their `run()` method invoked.
     *
     * @since [*next-version*]
     */
    public function testRun()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $reflect->modules = $modules = [
            $key1 = uniqid('key-') => $this->createModule($key1),
            $key2 = uniqid('key-') => $this->createModule($key2),
            $key3 = uniqid('key-') => $this->createModule($key3),
        ];
        $container = $this->createContainer();

        foreach ($modules as $_module) {
            $_module->expects($this->once())
                    ->method('run')
                    ->with($container);
        }

        $reflect->_run($container);
    }
}
