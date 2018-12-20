<?php

namespace RebelCode\Modular\Module\UnitTest;

use ArrayObject;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Modular\Module\AbstractModularModule;
use stdClass;
use Xpmock\TestCase;

/**
 * Tests {@see \RebelCode\Modular\Module\AbstractModularModule}.
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
     * @param array $methods Optional additional mock methods.
     *
     * @return MockObject|AbstractModularModule
     */
    public function createInstance(array $methods = [])
    {
        $builder = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                        ->disableOriginalConstructor()
                        ->setMethods(
                            array_merge(
                                $methods,
                                [
                                    '_createConfig',
                                    '_createContainer',
                                    '_createCompositeContainer',
                                    '_createAddCapableList',
                                ]
                            )
                        );

        return $builder->getMockForAbstractClass();
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
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf(
            'Dhii\Modular\Module\ModuleInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    /**
     * Tests the initialization method.
     *
     * @since [*next-version*]
     */
    public function testInit()
    {
        $subject = $this->createInstance(['_addSubModule']);
        $reflect = $this->reflect($subject);

        $reflect->subModules = [
            $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
            $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
        ];
        $reflect->subConfigs = [
            $this->getMockForAbstractClass('Dhii\Config\ConfigInterface'),
            $this->getMockForAbstractClass('Dhii\Config\ConfigInterface'),
        ];
        $reflect->subContainers = [
            $this->getMockForAbstractClass('Psr\Container\ContainerInterface'),
            $this->getMockForAbstractClass('Psr\Container\ContainerInterface'),
        ];
        $reflect->factories = [
            'service_1' => function () {
            },
            'service_2' => function () {
            },
        ];
        $reflect->extensions = [
            [
                'service_1' => function () {
                },
            ],
            [
                'service_2' => function () {
                },
            ],
        ];

        $subModules = [
            $sm1 = $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
            $sm2 = $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
            $sm3 = $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
        ];

        $subject->expects($this->exactly(3))
                ->method('_addSubModule')
                ->withConsecutive([$sm1], [$sm2], [$sm3]);

        $reflect->_init($subModules);

        $this->assertEmpty($reflect->subModules);
        $this->assertEmpty($reflect->subConfigs);
        $this->assertEmpty($reflect->subContainers);
        $this->assertEmpty($reflect->factories);
        $this->assertEmpty($reflect->extensions);
    }

    /**
     * Tests the run method to assert whether all sub modules are run.
     *
     * @since [*next-version*]
     */
    public function testRun()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $reflect->subModules = [
            $sm1 = $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
            $sm2 = $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
            $sm3 = $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
        ];

        $container = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');

        $sm1->expects($this->once())->method('run')->with($container);
        $sm2->expects($this->once())->method('run')->with($container);
        $sm3->expects($this->once())->method('run')->with($container);

        $subject->run($container);
    }

    /**
     * Tests the sub-module adder method.
     *
     * @since [*next-version*]
     */
    public function testAddSubModule()
    {
        $subject = $this->createInstance(['_getModuleServiceKey']);
        $reflect = $this->reflect($subject);

        $reflect->subModules = [
            'module' => $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
        ];

        $key = uniqid('key');
        $subModule = $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface');
        $subject->expects($this->once())->method('_getModuleServiceKey')->with($subModule)->willReturn($key);

        $expectedSubModules = $reflect->subModules;
        $expectedSubModules[$key] = $subModule;

        $reflect->_addSubModule($subModule);

        $this->assertEquals($expectedSubModules, $reflect->subModules);
    }

    /**
     * Tests the sub-module adder method with a module that sets up a container.
     *
     * @since [*next-version*]
     */
    public function testAddSubModuleSetup()
    {
        $subject = $this->createInstance(['_getModuleServiceKey']);
        $reflect = $this->reflect($subject);

        $reflect->subContainers = [
            $this->getMockForAbstractClass('Psr\Container\ContainerInterface'),
            $this->getMockForAbstractClass('Psr\Container\ContainerInterface'),
        ];

        $container = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $subModule = $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface');
        $subModule->expects($this->once())->method('setup')->willReturn($container);

        $expectedContainers = $reflect->subContainers;
        $expectedContainers[] = $container;

        $reflect->_addSubModule($subModule);

        $this->assertEquals($expectedContainers, $reflect->subContainers);
    }

    /**
     * Tests the sub-module adder method with a module that does not set up a container.
     *
     * @since [*next-version*]
     */
    public function testAddSubModuleNullContainerSetup()
    {
        $subject = $this->createInstance(['_getModuleServiceKey']);
        $reflect = $this->reflect($subject);

        $reflect->subContainers = [
            $this->getMockForAbstractClass('Psr\Container\ContainerInterface'),
            $this->getMockForAbstractClass('Psr\Container\ContainerInterface'),
        ];
        $reflect->subModules = [
            'module' => $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
        ];

        $subModule = $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface');
        $subModule->expects($this->once())->method('setup')->willReturn(null);

        $expectedContainers = $reflect->subContainers;

        $reflect->_addSubModule($subModule);

        $this->assertEquals($expectedContainers, $reflect->subContainers);
    }

    /**
     * Tests the sub-module adder method with a module that providers service factories and extensions.
     *
     * @since [*next-version*]
     */
    public function testAddSubModuleServiceProvider()
    {
        $subject = $this->createInstance(['_getModuleServiceKey']);
        $reflect = $this->reflect($subject);

        $reflect->factories = [
            'a' => function () {
            },
            'b' => function () {
            },
        ];
        $reflect->extensions = [
            [
                'a' => function () {
                },
            ],
            [
                'a' => function () {
                },
                'b' => function () {
                },
            ],
        ];

        $factories = [
            'b' => function () {
            },
            'c' => function () {
            },
        ];
        $extensions = [
            'c' => function () {
            },
            'd' => function () {
            },
        ];
        $subModule = $this->mockClassAndInterfaces('stdClass', [
            'Dhii\Modular\Module\ModuleInterface',
            'Interop\Container\ServiceProviderInterface',
        ]);
        $subModule->expects($this->once())->method('getFactories')->willReturn($factories);
        $subModule->expects($this->once())->method('getExtensions')->willReturn($extensions);

        $expectedFactories = $reflect->factories;
        $expectedFactories['b'] = $factories['b'];
        $expectedFactories['c'] = $factories['c'];

        $expectedExtensions = $reflect->extensions;
        $expectedExtensions[] = $extensions;

        $reflect->_addSubModule($subModule);

        $this->assertEquals($expectedFactories, $reflect->factories);
        $this->assertEquals($expectedExtensions, $reflect->extensions);
    }

    /**
     * Tests the sub-module adder method with a module that providers service factories and extensions.
     *
     * @since [*next-version*]
     */
    public function testAddSubModuleConfigProvider()
    {
        $subject = $this->createInstance(['_getModuleServiceKey']);
        $reflect = $this->reflect($subject);

        $reflect->subConfigs = [
            'a' => [uniqid('some-config')],
            'b' => [uniqid('some-config')],
        ];

        $config = [
            uniqid('key1') => uniqid('value'),
            uniqid('key2') => uniqid('value'),
            uniqid('key3') => uniqid('value'),
        ];
        $subModule = $this->mockClassAndInterfaces('stdClass', [
            'Dhii\Modular\Module\ModuleInterface',
            'RebelCode\Modular\Config\ConfigProviderInterface',
        ]);
        $subModule->expects($this->once())->method('getConfig')->willReturn($config);

        $expectedConfigs = $reflect->subConfigs;
        $expectedConfigs[] = $config;

        $reflect->_addSubModule($subModule);

        $this->assertEquals($expectedConfigs, $reflect->subConfigs);
    }

    /**
     * Tests the sub-modules container initialization method.
     *
     * @since [*next-version*]
     */
    public function testInitSubModulesContainer()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expected = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $parent = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');

        $reflect->subModules = [
            $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
            $this->getMockForAbstractClass('Dhii\Modular\Module\ModuleInterface'),
        ];

        $subject->expects($this->once())
                ->method('_createContainer')
                ->with($reflect->subModules, $parent)
                ->willReturn($expected);

        $actual = $reflect->_initSubModulesContainer($parent);

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests the config container initialization method.
     *
     * @since [*next-version*]
     */
    public function testInitConfigContainer()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $parent = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');

        $reflect->subConfigs = [
            $c1 = [uniqid('key1') => uniqid('val1')],
            $c2 = [uniqid('key2') => uniqid('val2')],
            $c3 = [uniqid('key3') => uniqid('val3')],
        ];

        $config1 = $this->getMockForAbstractClass('Dhii\Config\ConfigInterface');
        $config2 = $this->getMockForAbstractClass('Dhii\Config\ConfigInterface');
        $config3 = $this->getMockForAbstractClass('Dhii\Config\ConfigInterface');

        $subject->expects($this->exactly(3))
                ->method('_createConfig')
                ->withConsecutive([$c3], [$c2], [$c1])
                ->willReturnOnConsecutiveCalls($config3, $config2, $config1);

        $list = $this->getMockForAbstractClass('Dhii\Collection\AddCapableInterface');
        $list->expects($this->exactly(3))
             ->method('add')
             ->withConsecutive([$config3], [$config2], [$config1]);

        $subject->expects($this->atLeastOnce())
                ->method('_createAddCapableList')
                ->willReturn($list);

        $container = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $subject->expects($this->atLeastOnce())
                ->method('_createCompositeContainer')
                ->with($list)
                ->willReturn($container);

        $config = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $subject->expects($this->once())
                ->method('_createContainer')
                ->with(['config' => $container], $parent)
                ->willReturn($config);

        $actual = $reflect->_initConfigContainer($parent);

        $this->assertSame($config, $actual);
    }

    /**
     * Tests the services container initialization method.
     *
     * @since [*next-version*]
     */
    public function testInitServicesContainer()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        // Using scalars and objects instead of factory closures to allow the assertion to make
        // strict instance and value comparisons
        $reflect->factories = [
            'a' => $af = 'a_factory',
            'b' => $bf = new stdClass(),
            'c' => $cf = 3.3333333,
            'd' => $df = true,
        ];
        $reflect->extensions = [
            [
                'a' => $ae = new ArrayObject(),
            ],
            [
                'c' => $ce = 'c_extension',
            ],
        ];
        // Expect closures for extended services, since factories are wrapped.
        $expected = [
            'a' => function () {
            },
            'b' => $bf,
            'c' => function () {
            },
            'd' => $df,
        ];

        $parentCntr = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $container = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');

        $subject->expects($this->once())
                ->method('_createContainer')
                ->with($expected, $parentCntr)
                ->willReturn($container);

        $reflect->_initServicesContainer($parentCntr);

        $this->assertEquals($expected, $reflect->factories);
    }

    /**
     * Tests the sub-modules setup containers initialization method.
     *
     * @since [*next-version*]
     */
    public function testInitSubModulesSetupContainers()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $reflect->subContainers = [
            $c1 = $this->getMockForAbstractClass('Psr\Container\ContainerInterface'),
            $c2 = $this->getMockForAbstractClass('Psr\Container\ContainerInterface'),
            $c3 = $this->getMockForAbstractClass('Psr\Container\ContainerInterface'),
        ];

        $list = $this->getMockForAbstractClass('Dhii\Collection\AddCapableInterface');
        $subject->expects($this->atLeastOnce())
                ->method('_createAddCapableList')
                ->willReturn($list);

        $container = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $subject->expects($this->atLeastOnce())
                ->method('_createCompositeContainer')
                ->with($list)
                ->willReturn($container);

        $list->expects($this->exactly(3))
             ->method('add')
             ->withConsecutive([$c3], [$c2], [$c1]);

        $actual = $reflect->_initSubModulesSetupContainer();

        $this->assertSame($container, $actual);
    }

    /**
     * Tests the module service key getter method.
     *
     * @since [*next-version*]
     */
    public function testGetModuleServiceKey()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $key = uniqid('key');
        $expected = $key . '_module';

        $module = $this->getMockBuilder('Dhii\Modular\Module\ModuleInterface')
                       ->setMethods(['getKey', 'setup', 'run'])
                       ->getMockForAbstractClass();
        $module->expects($this->atLeastOnce())->method('getKey')->willReturn($key);

        $actual = $reflect->_getModuleServiceKey($module);

        $this->assertEquals($expected, $actual);
    }
}
