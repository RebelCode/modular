<?php

namespace RebelCode\Modular\Module\FuncTest;

use Xpmock\TestCase;
use RebelCode\Modular\Module\AbstractBaseFileConfigModule as TestSubject;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AbstractBaseFileConfigModule extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Module\AbstractBaseFileConfigModule';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock.
     *
     * @return TestSubject|MockObject
     */
    public function createInstance(array $methods = [])
    {
        return $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                    ->setMethods($methods)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass();
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
            'A valid instance of the test subject could not be created.'
        );

        $this->assertInstanceOf(
            'Dhii\Modular\Module\ModuleInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    /**
     * Tests the setup method to ensure that the container is set up correctly using the config and services read from
     * the files whose paths are retrieved from the internal moodule config.
     *
     * @since [*next-version*]
     */
    public function testSetup()
    {
        $subject = $this->createInstance(['_setupContainer', '_loadPhpConfigFile', '_getConfig']);

        $subject->expects($this->atLeastOnce())
                ->method('_getConfig')
                ->willReturn(
                    $internalCfg = $this->getMockForAbstractClass('Dhii\Config\ConfigInterface')
                );

        // Expect config and services file paths to be read from config
        $internalCfg->expects($this->exactly(2))
                    ->method('get')
                    ->withConsecutive(
                        [TestSubject::K_CONFIG_CONFIG_FILE_PATH],
                        [TestSubject::K_CONFIG_SERVICES_FILE_PATH]
                    )
                    ->willReturnOnConsecutiveCalls(
                        $configFilePath = uniqid('config-file-path-'),
                        $servicesFilePath = uniqid('services-file-path-')
                    );

        // Mock "file" config and services
        $config = [
            uniqid('key1-') => uniqid('val1-'),
            uniqid('key2-') => uniqid('val2-'),
        ];
        $services = [
            uniqid() => function () {
            },
            uniqid() => function () {
            },
        ];

        // Expect the config and services files to be loaded from file
        $subject->expects($this->exactly(2))
                ->method('_loadPhpConfigFile')
                ->withConsecutive(
                    [$configFilePath],
                    [$servicesFilePath]
                )
                ->willReturnOnConsecutiveCalls(
                    $config,
                    $services
                );

        // Expect the container to be set up using the config and services
        $subject->expects($this->once())
                ->method('_setupContainer')
                ->with($config, $services)
                ->willReturn(
                    $container = $this->getMockForAbstractClass('Psr\Container\ContainerInterface')
                );

        $actual = $subject->setup();

        $this->assertSame($container,  $actual);
    }
}
