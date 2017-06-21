<?php

namespace RebelCode\Modular\FuncTest\Module;

use RebelCode\Modular\Module\DependenciesAwareTrait;
use Xpmock\TestCase;

/**
 * Tests {@see RebelCode\Modular\Module\DependenciesAwareTrait}.
 *
 * @since [*next-version*]
 */
class DependenciesAwareTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\\Modular\\Module\\DependenciesAwareTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return DependenciesAwareTrait
     */
    public function createInstance()
    {
        return $this->getMockForTrait(static::TEST_SUBJECT_CLASSNAME);
    }

    /**
     * Tests the dependency getter and setter methods.
     *
     * @since [*next-version*]
     */
    public function testGetSetDependencies()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $reflect->_setDependencies($expected = array('test', 'dep', 'foobar'));

        $this->assertEquals($expected, $reflect->_getDependencies());
    }
}
