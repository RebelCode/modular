<?php

namespace RebelCode\Modular\Finder;

use ArrayIterator;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\File\Finder\AbstractFileFinder;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Iterator\NormalizeIteratorCapableTrait;
use Exception;
use Iterator;
use IteratorAggregate;
use IteratorIterator;
use SplFileInfo;
use Traversable;

/**
 * Concrete implementation of a module file finder.
 *
 * This implementation searches in a root directory for `module.php` files, recursively up to a given maximum depth.
 *
 * @since[*next-version*]
 */
class ModuleFileFinder extends AbstractFileFinder implements IteratorAggregate
{
    /*
     * Provides iterator normalization functionality.
     *
     * @since [*next-version*]
     */
    use NormalizeIteratorCapableTrait;

    /*
     * Provides functionality for creating invalid-argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

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
     * The regex pattern that is used to match file names.
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
             ->_setCallbackFilter(array($this, '_filter'))
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
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getIterator()
    {
        return $this->_normalizeIterator($this->_getPaths());
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createArrayIterator(array $array)
    {
        return new ArrayIterator($array);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createTraversableIterator(Traversable $traversable)
    {
        return new IteratorIterator($traversable);
    }
}
