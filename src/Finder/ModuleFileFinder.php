<?php

namespace RebelCode\Modular\Finder;

use ArrayIterator;
use Dhii\File\Finder\AbstractFileFinder;
use Iterator;
use MongoDB\Driver\Exception\RuntimeException;
use SplFileInfo;
use Traversable;

/**
 * ModuleFileFinder.
 *
 * @since[*next-version*]
 */
class ModuleFileFinder extends AbstractFileFinder implements Iterator
{
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
     * @param string $rootDir The directory path where searching will begin.
     * @param int $maxDepth How deep to recurse into the root directory.
     */
    public function __construct($rootDir, $maxDepth = 2)
    {
        $this->_setRootDir($rootDir)
             ->_setFilenameRegex(static::FILENAME_REGEX)
             ->_setMaxDepth($maxDepth)
             ->_setCallbackFilter(array($this, '_filter'))
             ->_construct()
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _filter(SplFileInfo $fileInfo)
    {
        if (!$fileInfo->isReadable()) {
            throw new Exception();
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
     * @inheritDoc
     */
    public function rewind()
    {
        $this->iterator = $this->_getIterator();
        $this->iterator->rewind();
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->iterator->valid();
    }
}
