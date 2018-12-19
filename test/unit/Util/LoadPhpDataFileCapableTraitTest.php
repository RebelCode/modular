<?php

namespace RebelCode\Modular\Util\FuncTest;

use Exception;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Modular\Util\LoadPhpDataFileCapableTrait as TestSubject;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class LoadPhpDataFileCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\Modular\Util\LoadPhpDataFileCapableTrait';

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
                     ->setMethods($methods)
                     ->getMockForTrait();

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

        $this->assertInternalType(
            'object',
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests the PHP data file load method.
     *
     * @since [*next-version*]
     */
    public function testLoadPhpDataFile()
    {
        $data = [
            uniqid('some-data'),
        ];
        $vfs = vfsStream::setup('dir');
        vfsStream::create([
            'file.php' => '<?php return ' . var_export($data, true) . ';',
        ], $vfs);

        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $actual = $reflect->_loadPhpDataFile($vfs->url() . '/file.php');
        $this->assertEquals($data, $actual);
    }

    /**
     * Tests the PHP data file load method to assert whether a runtime exception is thrown if the file is not found.
     *
     * @since [*next-version*]
     */
    public function testLoadPhpDataFileNonExistentFile()
    {
        $data = [
            uniqid('some-data'),
        ];
        $vfs = vfsStream::setup('dir');
        vfsStream::create([
            'file.php' => '<?php return ' . var_export($data, true) . ';',
        ], $vfs);

        $this->setExpectedException('RuntimeException');

        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $reflect->_loadPhpDataFile($vfs->url() . '/non-existent-file.php');
    }

    /**
     * Tests the PHP data file load method to assert whether exceptions thrown within the file are wrapped.
     *
     * @since [*next-version*]
     */
    public function testLoadPhpDataFileException()
    {
        $vfs = vfsStream::setup('dir');
        vfsStream::create([
            'file.php' => '<?php class TestCustomException extends Exception {} throw new TestCustomException();',
        ], $vfs);

        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        try {
            $reflect->_loadPhpDataFile($vfs->url() . '/file.php');
            $this->fail('Expected an exception to be thrown');
        } catch (Exception $exception) {
            $this->assertInstanceOf('TestCustomException', $exception->getPrevious());
        }
    }
}
