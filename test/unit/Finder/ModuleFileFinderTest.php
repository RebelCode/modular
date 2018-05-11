<?php

namespace RebelCode\Modular\UnitTest\Finder;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\visitor\vfsStreamPrintVisitor;
use RebelCode\Modular\Finder\ModuleFileFinder;
use Xpmock\TestCase;

/**
 * Tests {@see RebelCode\Modular\Finder\ModuleFileFinder}.
 *
 * @since[*next-version*]
 */
class ModuleFileFinderTest extends TestCase
{
    /**
     * Name of root directory.
     *
     * @since [*next-version*]
     */
    const ROOT_DIR_NAME = 'vendor';

    /**
     * Dummy contents for a module file.
     *
     * @since [*next-version*]
     */
    const DUMMY_MODULE_FILE_CONTENTS = '<?php return array()';

    /**
     * Dummy contents for a regular PHP file.
     *
     * @since [*next-version*]
     */
    const DUMMY_PHP_FILE_CONTENTS = '<?php echo "some dummy file"; ';

    /**
     * Dummy contents for a Composer JSON file.
     *
     * @since [*next-version*]
     */
    const DUMMY_COMPOSER_JSON_FILE_CONTENTS = '{name: "dummy-package"}';

    /**
     * Dummy contents for a a PHP class file.
     *
     * @since [*next-version*]
     */
    const DUMMY_PHP_CLASS_FILE_CONTENTS = '<?php class SomeClass {}';

    /**
     * Creates a new instance of the test subject.
     *
     * @since[*next-version*]
     *
     * @param string $rootDir  The root directory.
     * @param int    $maxDepth The maximum recursion depth from the root directory into subdirectories.
     *
     * @return ModuleFileFinder
     */
    public function createInstance($rootDir = '', $maxDepth = 1)
    {
        $instance = new ModuleFileFinder($rootDir, $maxDepth);

        return $instance;
    }

    /**
     * Tests the module file location functionality.
     *
     * @since[*next-version*]
     */
    public function testLocate()
    {
        $vfs = $this->_createFilesystem();
        $subject = $this->createInstance($vfs->url(), 2);

        $paths = $this->reflect($subject)->_getPaths();
        $paths = iterator_to_array($paths);

        $this->assertCount(3, $paths, 'Wrong number of module files found.');
    }

    /**
     * A mock filesystem.
     *
     * @since [*next-version*]
     *
     * @return vfsStreamDirectory
     */
    protected function _createFilesystem()
    {
        $vfs = vfsStream::setup(static::ROOT_DIR_NAME);
        $vendorDirStructure = array(
            'rebelcode' => array(
                'some-module' => array(
                    'src' => array(
                        'SomeClass.php' => static::DUMMY_PHP_FILE_CONTENTS,
                    ),
                    'module.php' => static::DUMMY_MODULE_FILE_CONTENTS,
                    'index.php' => static::DUMMY_PHP_FILE_CONTENTS,
                    'composer.json' => static::DUMMY_COMPOSER_JSON_FILE_CONTENTS,
                ),
                'another-module' => array(
                    'module.php' => static::DUMMY_MODULE_FILE_CONTENTS,
                ),
                'non-module' => array(
                    'some-file.php' => static::DUMMY_PHP_FILE_CONTENTS,
                    'config.php' => static::DUMMY_MODULE_FILE_CONTENTS,
                    'composer.json' => static::DUMMY_COMPOSER_JSON_FILE_CONTENTS,
                ),
            ),
            'symfony' => array(
                'finder' => array(
                    'src' => array(
                        'SomeClass.php' => static::DUMMY_PHP_CLASS_FILE_CONTENTS,
                    ),
                    'composer.json' => static::DUMMY_COMPOSER_JSON_FILE_CONTENTS,
                ),
                'rc-mod' => array(
                    'src' => array(
                        'SomeClass.php' => static::DUMMY_PHP_CLASS_FILE_CONTENTS,
                    ),
                    'module.php' => static::DUMMY_MODULE_FILE_CONTENTS,
                ),
            ),
        );
        vfsStream::create($vendorDirStructure, $vfs);

        // Uncommend below line to print directory structure.
        // vfsStream::inspect(new vfsStreamPrintVisitor(), $vfs);

        return $vfs;
    }
}
