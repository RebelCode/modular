<?php

namespace RebelCode\Modular\UnitTest\Factory;

use Dhii\Factory\FactoryInterface;
use RebelCode\Modular\Config\ConfigInterface as Cfg;
use RebelCode\Modular\Factory\CouldNotMakeModuleException;
use RebelCode\Modular\Factory\ModuleFactory;
use RebelCode\Modular\Module\Module;
use Xpmock\TestCase;

/**
 * Tests {@see RebelCode\Modular\Factory\ModuleFactory}.
 *
 * @since [*next-version*]
 */
class ModuleFactoryTest extends TestCase
{
    /**
     * The fully qualified name of the exception for module creation failure.
     *
     * @since [*next-version*]
     */
    const COULD_NOT_MAKE_MODULE_EXCEPTION = 'Dhii\\Modular\\Factory\\CouldNotMakeModuleExceptionInterface';

    /**
     * Creates a factory for testing purposes.
     *
     * @since [*next-version*]
     *
     * @param string $serviceId The ID of the module service definition.
     *
     * @return FactoryInterface
     */
    public function createFactory($serviceId = 'module')
    {
        $moduleServiceDefinition = function($service, $config) use ($serviceId) {
            if ($service !== $serviceId) {
                throw new CouldNotMakeModuleException('Service with given ID was not found.');
            }

            $key  = $config[Cfg::K_KEY];
            $deps = isset($config[Cfg::K_DEPENDENCIES])
                ? $config[Cfg::K_DEPENDENCIES]
                : array();
            $onLoad = isset($config[Cfg::K_ON_LOAD])
                ? $config[Cfg::K_ON_LOAD]
                : null;

            return new Module($key, $deps, $config, $onLoad);
        };

        $mock = $this->mock('Dhii\\Factory\\FactoryInterface')
            ->make($moduleServiceDefinition)
            ->new();

        return $mock;
    }

    /**
     * Tests the constructor.
     *
     * @since [*next-version*]
     */
    public function testConstructor()
    {
        $subject = new ModuleFactory($this->createFactory(), 'module');

        $this->assertInstanceof(
            'RebelCode\\Modular\\Factory\\ModuleFactory',
            $subject,
            'Created instance of test subject is not a valid instance.'
        );

        $this->assertInstanceof(
            'Dhii\\Modular\\Factory\\ModuleFactoryInterface',
            $subject,
            'Created instance of test subject does not implement the expected interface.'
        );
    }

    /**
     * Tests the module creation method to assert whether the module key is correctly determined from
     * the configuration data.
     *
     * @since [*next-version*]
     */
    public function testMakeModuleKey()
    {
        $subject = new ModuleFactory($this->createFactory(), 'module');
        $key     = 'test';
        $config  = array(
            Cfg::K_KEY          => $key,
            Cfg::K_DEPENDENCIES => array()
        );

        $module = $subject->makeModule($config);

        $this->assertEquals($key, $module->getKey());
    }

    /**
     * Tests the module creation method to assert whether the module dependencies are correctly
     * determined from the configuration data.
     *
     * @since [*next-version*]
     */
    public function testMakeModuleDeps()
    {
        $subject = new ModuleFactory($this->createFactory(), 'module');
        $deps    = array('dep1', 'dep2');
        $config  = array(
            Cfg::K_KEY          => 'test',
            Cfg::K_DEPENDENCIES => $deps
        );

        $module = $subject->makeModule($config);

        $this->assertEquals($deps, $module->getDependencies());
    }

    /**
     * Tests the module creation method to assert whether the module on-load function is correctly
     * determined from the configuration data, by ensuring that the callback in the configuration
     * is the same load callback that the module uses when `load()` is called on it.
     *
     * @since [*next-version*]
     */
    public function testMakeModuleOnLoad()
    {
        $subject = new ModuleFactory($this->createFactory(), 'module');
        $called  = 0;
        $onLoad  = function() use (&$called) {
            $called++;
        };
        $config = array(
            Cfg::K_KEY          => 'test',
            Cfg::K_DEPENDENCIES => array(),
            Cfg::K_ON_LOAD      => $onLoad
        );

        $module = $subject->makeModule($config);
        $module->load();

        $this->assertEquals(1, $called,
            sprintf('Module on-load function expected to be called once. Called %d times.', $called)
        );
    }

    /**
     * Tests the module creation method with the module key missing from the configuration to
     * assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testMakeModuleNoKey()
    {
        $subject = new ModuleFactory($this->createFactory(), 'module');
        $config  = array(
            Cfg::K_DEPENDENCIES => array()
        );

        $this->setExpectedException(static::COULD_NOT_MAKE_MODULE_EXCEPTION);

        $subject->makeModule($config);
    }

    /**
     * Tests the module creation method with the module dependencies missing from the configuration
     * to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testMakeModuleNoDeps()
    {
        $subject = new ModuleFactory($this->createFactory(), 'module');
        $config  = array(
            Cfg::K_DEPENDENCIES => array()
        );

        $this->setExpectedException(static::COULD_NOT_MAKE_MODULE_EXCEPTION);

        $subject->makeModule($config);
    }

    /**
     * Tests the module creation method with the module on-load function missing from the
     * configuration to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testMakeModuleEmptyConfig()
    {
        $subject = new ModuleFactory($this->createFactory(), 'module');
        $config  = array();

        $this->setExpectedException(static::COULD_NOT_MAKE_MODULE_EXCEPTION);

        $subject->makeModule($config);
    }
}
