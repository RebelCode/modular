<?php

namespace RebelCode\Modular\FuncTest\Loader;

use RebelCode\Modular\Iterator\DependencyModuleIterator;
use RebelCode\Modular\Loader\LoopMachineModuleLoader;
use RebelCode\Modular\Module\ModuleInterface;
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
    const MODULE_CLASSNAME = 'RebelCode\\Modular\\Module\\ModuleInterface';

    /**
     * Create a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $modules The list of modules.
     *
     * @return LoopMachineModuleLoader
     */
    public function createInstance(array $modules = array())
    {
        return new DependencyModuleIterator($modules);
    }

    /**
     * Creates an instance of a module.
     *
     * @since [*next-version*]
     *
     * @param string $key The module key.
     * @param array  $deps The keys of the dependency modules.
     * @param callable $load The load callback.
     *
     * @return ModuleInterface
     */
    public function createModuleInstance($key, array $deps = array(), $load = null)
    {
        return $this->mock(static::MODULE_CLASSNAME)
            ->getKey($key)
            ->getName($key)
            ->getDependencies($deps)
            ->load($load)
            ->new();
    }

    /**
     * Tests the iteration.
     *
     * @since [*next-version*]
     */
    public function testIteration()
    {
        $instance = $this->createInstance(array(
            $this->createModuleInstance('a', array('b')),
            $this->createModuleInstance('b', array('c', 'd')),
            $this->createModuleInstance('c'),
            $this->createModuleInstance('d', array('a', 'c')),
        ));

        $result   = array_keys(iterator_to_array($instance));
        $expected = array('c', 'd', 'b', 'a');

        $this->assertEquals($expected, $result);
    }
}
