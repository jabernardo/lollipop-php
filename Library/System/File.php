<?php

namespace Lollipop\System;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Number;

/**
 * System\File Class
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for system file
 * 
 */
class File
{
    /**
     * Alias is_readable
     *
     * @param  string   $filename   Filename
     * @return boolean
     */
    static function isReadable($filename) {
        return \is_readable($filename);
    }

    /**
     * Alias is_writable
     *
     * @param  string   $filename
     * @return boolean
     */
    static function isWritable($filename) {
        return \is_writable($filename);
    }

    /**
     * Create or updates file
     *
     * @param   string  $filename   File to create or update
     * @param   string  $contents   Contents to write into file
     * @param   bool    $overwriteExisting  Overwrite existing file
     */
    static function write($filename, $contents, $overwriteExisting = true) {
        if (file_exists($filename) && self::isWritable($filename)) {
            if ($overwriteExisting) unlink($filename);
        }

        file_put_contents($filename, $contents);
    }

    /**
     * Gets the contents of a file
     *
     * @param   string  $filename   File to be read
     *
     * @return  string
     */
    static function read($filename) {
        if (file_exists($filename) && self::isReadable($filename)) {
            return file_get_contents($filename);
        }

        return NULL;
    }

    /**
     * Gets the size of a file
     *
     * @param   string  $filename   File
     * @param   bool    $returnFormatted    Returns formatted size in string
     *
     * @return  long
     */
    static function size($filename, $returnFormatted = false) {
        if ($returnFormatted && self::isReadable($filename)) {
            return Number::readableSize((double)filesize($filename));
        }

        return file_exists($filename) ? filesize($filename) : 0;
    }

    /**
     * Alias unlink
     *
     * @see     unlink();
     * @param   string  Filename of file to be deleted
     */
    static function delete($filename) {
        if (file_exists($filename)) unlink($filename);
    }

    /**
     * Checks if file exists
     *
     * @see     file_exists();
     * @param   string  $filename   Filename
     *
     * @return  bool
     */
    static function exists($filename) {
        return file_exists($filename);
    }
    
    /**
     * Alias rename file
     * 
     * @param   string  $src    Source file
     * @param   string  $dest   Destination file
     * @return  int
     * 
     */
    static function rename($src, $dest) {
        if (self::exists($dest)) {
            throw new \Lollipop\Exception\Runtime('File already exists');
        }
        
        if (is_uploaded_file($src)) {
            return \move_uploaded_file($src, $dest);
        }
        
        return \rename($src, $dest);
    }
}
