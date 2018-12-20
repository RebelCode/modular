<?php

namespace RebelCode\Modular\Util;

use Exception;
use RuntimeException;

/**
 * Functionality for loading PHP files and retrieving their returned data.
 *
 * @since [*next-version*]
 */
trait LoadPhpDataFileCapableTrait
{
    /**
     * Loads a PHP file and returns the data returned from that file.
     *
     * Since module systems have varying loading mechanisms, it is not safe to assume that the current working directory
     * will be equivalent to the module's directory. Therefore, it is recommended to use absolute paths for the file
     * path argument.
     *
     * @since [*next-version*]
     *
     * @param string $filePath The path to the PHP file. Absolute paths are recommended.
     *
     * @throws RuntimeException If the file could not be read.
     *
     * @return mixed The data that was returned from the PHP file.
     */
    protected function loadPhpDataFile($filePath)
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new RuntimeException(
                $this->__('PHP file does not exist or not readable'),
                null,
                null
            );
        }

        try {
            $config = require $filePath;
        } catch (Exception $exception) {
            throw new RuntimeException(
                $this->__('The PHP file triggered an exception'),
                null,
                $exception
            );
        }

        return $config;
    }

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
