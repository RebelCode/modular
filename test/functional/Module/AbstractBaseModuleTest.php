<?php

namespace RebelCode\Modular\FuncTest\Module;

use Dhii\Data\Container\ContainerFactoryInterface;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Modular\Module\AbstractBaseModule as TestSubject;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AbstractBaseModuleTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\\Modular\\Module\\AbstractBaseModule';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param string[] $methods The methods to mock.
     *
     * @return TestSubject|MockObject
     */
    public function createInstance(array $methods = [])
    {
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->disableOriginalConstructor()
                     ->setMethods(
                         array_merge(
                             [
                                 'setup',
                                 'run',
                             ],
                             $methods
                         )
                     )
                     ->getMockForAbstractClass();

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

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'Subject is not a valid instance.'
        );

        $this->assertInstanceOf(
            'Dhii\Modular\Module\ModuleInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    /**
     * Tests the constructor to assert whether the parameter values are correctly stored in the test subject instance.
     *
     * @since [*next-version*]
     */
    public function testConstructor()
    {
        $configData           = [
            'key'          => uniqid('key-'),
            'dependencies' => [
                uniqid('dependencies-'),
                uniqid('dependencies-'),
            ],
        ];
        $config               = $this->getMockForAbstractClass('Dhii\Config\ConfigInterface');
        $configFactory        = $this->getMockForAbstractClass('Dhii\Config\ConfigFactoryInterface');
        $containerFactory     = $this->getMockForAbstractClass('Dhii\Data\Container\ContainerFactoryInterface');
        $compContainerFactory = $this->getMockForAbstractClass('Dhii\Data\Container\ContainerFactoryInterface');

        $configFactory->expects($this->once())
                      ->method('make')
                      ->with(['data' => $configData])
                      ->willReturn($config);

        $config->expects($this->once())
               ->method('has')
               ->with('dependencies')
               ->willReturn(true);
        $config->expects($this->exactly(2))
               ->method('get')
               ->withConsecutive(['key'], ['dependencies'])
               ->willReturnOnConsecutiveCalls($configData['key'], $configData['dependencies']);

        $builder = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                        ->enableOriginalConstructor()
                        ->setConstructorArgs([$configData, $configFactory, $containerFactory, $compContainerFactory]);

        $subject = $builder->getMockForAbstractClass();
        $reflect = $this->reflect($subject);

        $this->assertEquals($configData['key'], $subject->getKey(),
            'Module key does not match key in config.');
        $this->assertEquals($configData['dependencies'], $subject->getDependencies(),
            'Module dependencies do not match dependencies in config.');
        $this->assertSame($configFactory, $reflect->_getConfigFactory(),
            'Module config factory is incorrect');
        $this->assertSame($containerFactory, $reflect->_getContainerFactory(),
            'Module container factory is incorrect');
        $this->assertSame($compContainerFactory, $reflect->_getCompositeContainerFactory(),
            'Module composite container factory is incorrect');
    }

    /**
     * Tests the public key getter method to assert whether the retrieved key is correct.
     *
     * @since [*next-version*]
     */
    public function testGetKey()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $key = uniqid('key-');

        $reflect->_setKey($key);

        $this->assertEquals($key, $subject->getKey(), 'Set and retrieved keys are not the same.');
    }

    /**
     * Tests the public dependencies getter method to assert whether the retrieved dependency list is correct.
     *
     * @since [*next-version*]
     */
    public function testGetDependencies()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $deps = [
            uniqid('dep-'),
            uniqid('dep-'),
            uniqid('dep-'),
        ];

        $reflect->_setDependencies($deps);

        $this->assertEquals($deps, $subject->getDependencies(), 'Set and retrieved dependencies are not the same.');
    }

    /**
     * Tests the container creation method.
     *
     * @since [*next-version*]
     */
    public function testCreateContainer()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $parent = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $definitions = [
            uniqid('definition-'),
            uniqid('definition-'),
            uniqid('definition-'),
        ];
        $config = [
            ContainerFactoryInterface::K_DATA => $definitions,
            'parent' => $parent,
        ];
        $container = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');

        $reflect->containerFactory = $this->getMockForAbstractClass('Dhii\Data\Container\ContainerFactoryInterface');
        $reflect->containerFactory->expects($this->once())
                                  ->method('make')
                                  ->with($config)
                                  ->willReturn($container);

        $actual = $reflect->_createContainer($definitions, $parent);

        $this->assertEquals($container, $actual, 'Created container is not the container created by the factory.');
    }

    /**
     * Tests the container setup to assert whether the configs are correctly merged together and whether the merged
     * config and the services are used to create the final container.
     *
     * @since [*next-version*]
     */
    public function testSetupContainer()
    {
        $subject = $this->createInstance(['_createCompositeContainer', '_createContainer', '_createConfig']);
        $reflect = $this->reflect($subject);

        $internalConfig = [];
        $paramConfig = [];
        $fullConfig = [];
        $config = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');

        $paramServices = [];
        $services = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $container = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');

        $subject->expects($this->exactly(2))
            ->method('_createCompositeContainer')
            ->withConsecutive(
                $this->equalTo([$internalConfig, $paramConfig]),
                $this->equalTo([$config, $services])
            )
            ->willReturnOnConsecutiveCalls(
                $fullConfig,
                $container
            );

        $subject->expects($this->once())
            ->method('_createConfig')
            ->with($fullConfig)
            ->willReturn($config);

        $subject->expects($this->once())
                ->method('_createContainer')
                ->with($paramServices)
                ->willReturn($services);

        $actual = $reflect->_setupContainer($paramConfig, $paramServices);

        $this->assertSame($container, $actual, 'Expected and returned containers do not match');
    }

    /**
     * Tests the PHP config file load method.
     *
     * @since [*next-version*]
     */
    public function testLoadPhpConfigFile()
    {
        $config = [
            uniqid('key-') => uniqid('val-'),
            uniqid('key-') => uniqid('val-'),
            uniqid('key-') => uniqid('val-'),
        ];

        $vfs = vfsStream::setup('config');
        vfsStream::create(
            [
                'config.php' => '<?php return '.var_export($config, true).';',
            ],
            $vfs
        );

        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $actual = $reflect->_loadPhpConfigFile($vfs->url().'/config.php');

        $this->assertEquals($config, $actual);
    }
}
