<?php

namespace RebelCode\Modular\Finder;

use ArrayIterator;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\File\Finder\AbstractFileFinder;
use Dhii\I18n\StringTranslatingTrait;
use Exception;
use Iterator;
use SplFileInfo;

/**
 * Concrete implementation of a module file finder.
 *
 * This implementation searches in a root directory for `module.php` files, recursively up to a given maximum depth.
 *
 * @since[*next-version*]
 */
class ModuleFileFinder extends AbstractFileFinder implements Iterator
{
    /*
     * Provides functionality for creating runtime exceptions.
     *
     * @since [*next-version*]
     */
    use CreateRuntimeExceptionCapableTrait;

    /*
     * Provides string translating functionality.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /**
     * The regex pattern that is used to match filenames.
     *
     * @since [*next-version*]
     */
    const FILENAME_REGEX = '/[\\/\\\\]module\.php$/';

    /**
     * The delegate iterator.
     *
     * @since[*next-version*]
     *
     * @var ArrayIterator
     */
    protected $iterator;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $rootDir  The directory path where searching will begin.
     * @param int    $maxDepth How deep to recurse into the root directory.
     */
    public function __construct($rootDir, $maxDepth = 2)
    {
        $this->_setRootDir($rootDir)
             ->_setFilenameRegex(static::FILENAME_REGEX)
             ->_setMaxDepth($maxDepth)
             ->_setCallbackFilter([$this, '_filter'])
             ->_construct();
    }

    /**
     * Filters a found file.
     *
     * @since [*next-version*]
     *
     * @param SplFileInfo $fileInfo The file info.
     *
     * @throws Exception If the file is not readable.
     *
     * @return bool True if the file is allowed, false if it is rejected.
     */
    protected function _filter(SplFileInfo $fileInfo)
    {
        if (!$fileInfo->isReadable()) {
            throw $this->_createRuntimeException(
                $this->__('The module file is not readable'),
                null,
                null
            );
        }

        return true;
    }

    /**
     * Retrieves the iterator that can be used to iterate over found files.
     *
     * @since[*next-version*]
     *
     * @return Iterator
     */
    protected function _getIterator()
    {
        $paths = $this->_getPaths();

        return ($paths instanceof Iterator)
            ? $paths
            : new ArrayIterator($paths);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function rewind()
    {
        $this->iterator = $this->_getIterator();
        $this->iterator->rewind();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function valid()
    {
        return $this->iterator->valid();
    }
}
