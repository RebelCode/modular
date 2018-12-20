<?php

namespace RebelCode\Modular\Module\FuncTest;

use RebelCode\Modular\Module\ModuleTrait as TestSubject;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class ModuleTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Module\ModuleTrait';

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
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods($this->mergeValues($methods, []))
                     ->getMockForTrait();

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
     * Tests the initModule() method to assert whether the key and dependencies setters are invoked.
     *
     * @since [*next-version*]
     */
    public function testInitModule()
    {
        $subject = $this->createInstance(['_setKey', '_setDependencies']);
        $reflect = $this->reflect($subject);

        $key = uniqid('key');
        $deps = [
            uniqid('dep1'),
            uniqid('dep2'),
            uniqid('dep3'),
        ];

        $subject->expects($this->once())->method('_setKey')->with($key);
        $subject->expects($this->once())->method('_setDependencies')->with($deps);

        $reflect->initModule($key, $deps);
    }

    /**
     * Tests the initModule() method to assert whether the key is correctly saved and can be retrieved.
     *
     * @since [*next-version*]
     */
    public function testInitModuleGetKey()
    {
        $subject = $this->createInstance(['_setDependencies']);
        $reflect = $this->reflect($subject);

        $key = uniqid('key');
        $deps = [
            uniqid('dep1'),
            uniqid('dep2'),
            uniqid('dep3'),
        ];

        $subject->expects($this->once())->method('_setDependencies')->with($deps);

        $reflect->initModule($key, $deps);

        $this->assertEquals($key, $subject->getKey());
    }

    /**
     * Tests the initModule() method to assert whether the dependencies are correctly saved and can be retrieved.
     *
     * @since [*next-version*]
     */
    public function testInitModuleGetDependencies()
    {
        $subject = $this->createInstance(['_setKey']);
        $reflect = $this->reflect($subject);

        $key = uniqid('key');
        $deps = [
            uniqid('dep1'),
            uniqid('dep2'),
            uniqid('dep3'),
        ];

        $subject->expects($this->once())->method('_setKey')->with($key);

        $reflect->initModule($key, $deps);

        $this->assertEquals($deps, $subject->getDependencies());
    }
}
