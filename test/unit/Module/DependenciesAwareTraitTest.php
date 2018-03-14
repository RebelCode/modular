<?php

namespace RebelCode\Modular\UnitTest\Module;

use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Modular\Module\DependenciesAwareTrait as TestSubject;
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
     * @return TestSubject|MockObject
     */
    public function createInstance()
    {
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods(
                         [
                             '_normalizeIterable',
                         ]
                     )->getMockForTrait();

        return $mock;
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

        $deps = [
            uniqid('dep-'),
            uniqid('dep-'),
            uniqid('dep-'),
        ];
        $nDeps = [
            uniqid('dep-'),
            uniqid('dep-'),
            uniqid('dep-'),
        ];

        $subject->expects($this->once())
                ->method('_normalizeIterable')
                ->with($deps)
                ->willReturn($nDeps);

        $reflect->_setDependencies($deps);

        $this->assertEquals($nDeps, $reflect->_getDependencies(), 'Retrieved dependencies are incorrect.');
    }

    /**
     * Tests the dependency getter and setter methods with an invalid input to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testGetSetDependenciesInvalid()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $deps = [
            uniqid('dep-'),
            uniqid('dep-'),
            uniqid('dep-'),
        ];

        $subject->expects($this->once())
                ->method('_normalizeIterable')
                ->with($deps)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_setDependencies($deps);
    }
}
