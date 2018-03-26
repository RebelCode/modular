<?php

namespace RebelCode\Modular\FuncTest\Module;

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
     * @return TestSubject|MockObject
     */
    public function createInstance()
    {
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods(
                         [
                             'setup',
                             'run',
                         ]
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

        $definitions = [
            uniqid('definition-'),
            uniqid('definition-'),
            uniqid('definition-'),
        ];
        $config = [
            'definitions' => $definitions,
        ];
        $container = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');

        $reflect->containerFactory = $this->getMockForAbstractClass('Dhii\Data\Container\ContainerFactoryInterface');
        $reflect->containerFactory->expects($this->once())
                                  ->method('make')
                                  ->with($config)
                                  ->willReturn($container);

        $actual = $reflect->_createContainer($definitions);

        $this->assertEquals($container, $actual, 'Created container is not the container created by the factory.');
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
                'config.php' => '<?php return ' . var_export($config, true) . ';',
            ],
            $vfs
        );

        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $actual = $reflect->_loadPhpConfigFile($vfs->url() . '/config.php');

        $this->assertEquals($config, $actual);
    }
}
