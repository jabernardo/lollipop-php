<?php

namespace Lollipop\System;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Number;

/**
 * System\Directory Class
 *
 * @version     1.0.0
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for system directory
 * 
 */
class Directory
{
    /**
     * Checks if directory exists
     *
     * @param   string  $directory  Directory
     *
     * @return  bool
     */
    static function exists($directory) {
        return is_dir($directory) && is_readable($directory);
    }

    /**
     * Returns the contents of directory
     *
     * @param   string  $directory  Path to directory
     *
     * @return  array
     */
    static function contents($directory) {
        if (!self::exists($directory)) return null;

        $dircontents = array();

        $contents = scandir($directory);

        foreach ($contents as $content) {
            if ($content != '.' && $content != '..') {
                array_push($dircontents, $content);
            }
        }

        return $dircontents;
    }

    /**
     * Deletes a directory
     *
     * @param   string  $directory  Path
     * @param   bool    $force      Force remove file contents
     *
     * @param   bool
     */
    static function delete($directory, $force = false) {
        if (self::exists($directory)) {
            /**
             * Check for files
             *
             */
            $contents = scandir($directory);

            if ($force) {
                foreach ($contents as $content) {
                    if ($content != '.' && $content != '..') {
                        if (self::exists($directory . '/' . $content)) {
                            self::delete($directory . '/' . $content, true);
                        } else {
                            self::delete($directory . '/' . $content);
                        }
                    }
                }

                rmdir($directory);
            } else {
                $contents = scandir($directory);

                rmdir($directory);
            }

            if (!self::exists($directory)) return true;
        }

        return false;
    }
}

?>
