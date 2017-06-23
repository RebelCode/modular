<?php

namespace RebelCode\Modular\Finder;

use Dhii\File\Finder\AbstractFileFinder;
use SplFileInfo;

/**
 * ModuleFileFinder.
 *
 * @since[*next-version*]
 */
class ModuleFileFinder extends AbstractFileFinder
{
    /**
     * The regex pattern that is used to match filenames.
     *
     * @since [*next-version*]
     */
    const FILENAME_REGEX = '/[\\/\\\\]module\.php$/';

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
        return $fileInfo->isReadable();
    }
}
