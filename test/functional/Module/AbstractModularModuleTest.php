<?php

namespace RebelCode\Modular\Module\FuncTest;

use Dhii\Cache\MemoryMemoizer;
use Dhii\Collection\AddCapableOrderedList;
use Dhii\Config\DereferencingConfigMap;
use Dhii\Data\Container\CompositeContainer;
use Dhii\Di\CachingContainer;
use Dhii\Di\ContainerAwareCachingContainer;
use Dhii\Modular\Module\ModuleInterface;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Container\ContainerInterface;
use RebelCode\Modular\Module\AbstractModularModule as TestSubject;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AbstractModularModuleTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Module\AbstractModularModule';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param string            $key
     * @param string[]          $dependencies
     * @param ModuleInterface[] $subModules
     *
     * @return TestSubject|MockObject The new instance.
     */
    public function createInstance($key, $dependencies, $subModules)
    {
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setConstructorArgs([$key, $dependencies, $subModules])
                     ->setMethods([
                         '_createContainer',
                         '_createCompositeContainer',
                         '_createConfig',
                         '_createAddCapableList',
                     ])
                     ->getMockForAbstractClass();

        $mock->method('_createContainer')->willReturnCallback(function ($data, $parent = null) {
            return new ContainerAwareCachingContainer($data, new MemoryMemoizer(), $parent);
        });
        $mock->method('_createCompositeContainer')->willReturnCallback(function ($containers) {
            return new CompositeContainer($containers);
        });
        $mock->method('_createConfig')->willReturnCallback(function ($data) {
            return new DereferencingConfigMap($data);
        });
        $mock->method('_createAddCapableList')->willReturnCallback(function () {
            return new AddCapableOrderedList();
        });

        return $mock;
    }

    /**
     * Creates a mock module instance.
     *
     * @since [*next-version*]
     *
     * @param string                  $key
     * @param string[]                $deps
     * @param array                   $factories
     * @param array                   $extensions
     * @param array                   $config
     * @param ContainerInterface|null $container
     *
     * @return MockObject|ModuleInterface
     */
    public function createModule($key, $deps, $factories = [], $extensions = [], $config = [], $container = null)
    {
        $builder = $this->mockClassAndInterfaces('stdClass', [
            'Dhii\Modular\Module\ModuleInterface',
            'Dhii\Modular\Module\DependenciesAwareInterface',
            'Interop\Container\ServiceProviderInterface',
            'RebelCode\Modular\Config\ConfigProviderInterface',
        ]);

        $builder->disableOriginalConstructor()
                ->setMethods([
                    'getKey',
                    'getDependencies',
                    'getConfig',
                    'getFactories',
                    'getExtensions',
                    'setup',
                    'run',
                ]);

        $mock = $builder->getMockForAbstractClass();
        $mock->method('getKey')->willReturn($key);
        $mock->method('getDependencies')->willReturn($deps);
        $mock->method('getFactories')->willReturn($factories);
        $mock->method('getExtensions')->willReturn($extensions);
        $mock->method('getConfig')->willReturn($config);
        $mock->method('setup')->willReturn($container);

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
     * @return MockBuilder The object that extends and implements the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance('', [], []);

        $this->assertInstanceOf(
            'Dhii\Modular\Module\ModuleInterface',
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests the constructor and the setup to assert whether all important information is included in the resulting
     * container instance.
     *
     * @since [*next-version*]
     */
    public function testConstructorSetup()
    {
        $cntrA = new CachingContainer([
            'from_cntr_a' => function () {
                return 'hello from cntr a';
            },
        ], new MemoryMemoizer());

        $cntrB = new CachingContainer([
            'from_cntr_b' => function () {
                return 'goodbye from cntr b';
            },
        ], new MemoryMemoizer());

        $subModuleA = $this->createModule(
            'foo',
            [],
            $subFactoriesA = [
                'a_service1' => function () {
                    return 'a_service1';
                },
                'a_service2' => function () {
                    return 'a_service2';
                },
            ],
            $subExtensionsA = [],
            $configA = [
                'cfg1' => 'from module a',
                'cfg2' => 'from module a',
            ],
            $cntrA
        );

        $subModuleB = $this->createModule(
            'bar',
            ['foo'],
            $subFactoriesB = [
                'b_service1' => function () {
                    return 'b_service1';
                },
            ],
            $subExtensionsB = [
                'a_service2' => function ($c, $p) {
                    return $p . '!!';
                },
            ],
            $configB = [],
            $cntrB
        );

        $subModuleC = $this->createModule(
            'lorem',
            ['foo'],
            $subFactoriesB = [
            ],
            $subExtensionsC = [
                'a_service1' => function ($c, $p) {
                    return '!!' . $p;
                },
            ],
            $configC = [
                'cfg1' => 'from module c',
                'cfg3' => 'from module c',
            ],
            $cntrC = null
        );

        $key = uniqid('key');
        $deps = [
            uniqid('dep1'),
            uniqid('dep2'),
            uniqid('dep3'),
        ];
        $subModules = [
            $subModuleA,
            $subModuleB,
            $subModuleC,
        ];

        $subject = $this->createInstance($key, $deps, $subModules);

        $container = $subject->setup();

        // Get modules from container
        $this->assertSame($subModuleA, $container->get('foo_module'));
        $this->assertSame($subModuleB, $container->get('bar_module'));
        $this->assertSame($subModuleC, $container->get('lorem_module'));
        // Get services registered via SP pattern from container
        $this->assertEquals('!!a_service1', $container->get('a_service1'));
        $this->assertEquals('a_service2!!', $container->get('a_service2'));
        $this->assertEquals('b_service1', $container->get('b_service1'));
        // Get services registered via setup() from container
        $this->assertEquals('hello from cntr a', $container->get('from_cntr_a'));
        $this->assertEquals('goodbye from cntr b', $container->get('from_cntr_b'));

        // Test config
        $config = $container->get('config');
        $this->assertEquals('from module c', $config->get('cfg1'));
        $this->assertEquals('from module a', $config->get('cfg2'));
        $this->assertEquals('from module c', $config->get('cfg3'));
    }
}
