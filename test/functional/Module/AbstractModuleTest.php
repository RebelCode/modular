<?php

namespace RebelCode\Modular\FuncTest\Module;

use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;
use RebelCode\Modular\Module\AbstractModule as TestSubject;
use ReflectionClass;
use ReflectionException;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AbstractModuleTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\\Modular\\Module\\AbstractModule';

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
                     ->setMethods($this->mergeValues($methods, ['run']))
                     ->getMockForAbstractClass();

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

        $this->assertInstanceOf(
            'Dhii\Modular\Module\DependenciesAwareInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );

        $this->assertInstanceOf(
            'Interop\Container\ServiceProviderInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );

        $this->assertInstanceOf(
            'RebelCode\Modular\Config\ConfigProviderInterface',
            $subject,
            'Test subject does not implement expected interface.'
        );
    }

    /**
     * Tests the constructor to assert whether the parameter values are correctly stored in the test subject instance.
     *
     * @since [*next-version*]
     * @throws ReflectionException
     */
    public function testConstructor()
    {
        $subject = $this->createInstance(['initModule', 'loadPhpDataFile']);

        $key = uniqid('key');
        $deps = [
            uniqid('dep1'),
            uniqid('dep2'),
            uniqid('dep3'),
        ];
        $subject->expects($this->once())->method('initModule')->with($key, $deps);

        $configFile = uniqid('config-file');
        $config = [
            uniqid('cfg-key') => uniqid('cfg-val'),
            uniqid('cfg-key') => uniqid('cfg-val'),
        ];
        $servicesFile = uniqid('services-file');
        $services = [
            uniqid('svc-key') => uniqid('svc-val'),
            uniqid('svc-key') => uniqid('svc-val'),
        ];

        $subject->expects($this->exactly(2))
                ->method('loadPhpDataFile')
                ->withConsecutive([$configFile], [$servicesFile])
                ->willReturnOnConsecutiveCalls($config, $services);

        $reflect = new ReflectionClass($subject);
        $constructor = $reflect->getConstructor();

        $constructor->invokeArgs($subject, [$key, $deps, $configFile, $servicesFile]);
    }

    /**
     * Tests the public config getter method to assert whether the retrieved config is correct.
     *
     * @since [*next-version*]
     * @throws ReflectionException
     */
    public function testGetConfig()
    {
        $config = [
            uniqid('key') => uniqid('val'),
            uniqid('key') => uniqid('val'),
        ];
        $cfgExport = var_export($config, true);

        $vfs = vfsStream::setup('config');
        vfsStream::create([
            'config-file.php' => sprintf('<?php return %s;', $cfgExport),
        ], $vfs);
        $configFile = $vfs->url() . '/config-file.php';

        $subject = $this->createInstance();
        $reflect = new ReflectionClass($subject);
        $constructor = $reflect->getConstructor();

        $constructor->invokeArgs($subject, ['key', [], $configFile, null]);

        $this->assertEquals($config, $subject->getConfig());
    }

    /**
     * Tests the public service factories getter method to assert whether the retrieved service factories are correct.
     *
     * @since [*next-version*]
     * @throws ReflectionException
     */
    public function testGetFactories()
    {
        $services = [
            uniqid('key') => uniqid('pretend-im-a-closure'),
            uniqid('key') => uniqid('pretend-im-a-closure'),
        ];
        $svcExport = var_export($services, true);

        $vfs = vfsStream::setup('services');
        vfsStream::create([
            'services-file.php' => sprintf('<?php return %s;', $svcExport),
        ], $vfs);
        $servicesFile = $vfs->url() . '/services-file.php';

        $subject = $this->createInstance();
        $reflect = new ReflectionClass($subject);
        $constructor = $reflect->getConstructor();

        $constructor->invokeArgs($subject, ['key', [], null, $servicesFile]);

        $this->assertEquals($services, $subject->getFactories());
    }

    /**
     * Tests the public service factories getter method to assert whether the retrieved service factories are
     * correct, when the services file returns a service provider instance.
     *
     * @since [*next-version*]
     * @throws ReflectionException
     */
    public function testGetFactoriesServiceProvider()
    {
        $services = [
            uniqid('key') => uniqid('pretend-im-a-closure'),
            uniqid('key') => uniqid('pretend-im-a-closure'),
        ];
        $svcExport = var_export($services, true);

        $vfs = vfsStream::setup('services');
        vfsStream::create([
            'services-file.php' => $t = sprintf(
                '<?php return new \RebelCode\Modular\Container\ServiceProvider(%s, []);',
                $svcExport
            ),
        ], $vfs);
        $servicesFile = $vfs->url() . '/services-file.php';

        $subject = $this->createInstance();
        $reflect = new ReflectionClass($subject);
        $constructor = $reflect->getConstructor();

        $constructor->invokeArgs($subject, ['key', [], null, $servicesFile]);

        $this->assertEquals($services, $subject->getFactories());
    }

    /**
     * Tests the extensions getter method to assert whether the retrieved extensions are correct, when the services
     * file returns a service provider instance.
     *
     * @since [*next-version*]
     * @throws ReflectionException
     */
    public function testGetExtensions()
    {
        $extensions = [
            uniqid('key') => uniqid('pretend-im-a-closure'),
            uniqid('key') => uniqid('pretend-im-a-closure'),
        ];
        $extExport = var_export($extensions, true);

        $vfs = vfsStream::setup('services');
        vfsStream::create([
            'services-file.php' => $t = sprintf(
                '<?php return new \RebelCode\Modular\Container\ServiceProvider([], %s);',
                $extExport
            ),
        ], $vfs);
        $servicesFile = $vfs->url() . '/services-file.php';

        $subject = $this->createInstance();
        $reflect = new ReflectionClass($subject);
        $constructor = $reflect->getConstructor();

        $constructor->invokeArgs($subject, ['key', [], null, $servicesFile]);

        $this->assertEquals($extensions, $subject->getExtensions());
    }

    /**
     * Tests the setup() method to assert whether the result is null.
     *
     * @since [*next-version*]
     * @throws ReflectionException
     */
    public function testSetup()
    {
        $subject = $this->createInstance();

        $this->assertNull($subject->setup());
    }
}
