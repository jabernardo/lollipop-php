<?php
    namespace Lollipop;

    /**
     * FileSystem Class
     *
     * @version     1.0
     * @author      John Aldrich Bernardo
     * @email       4ldrich@protonmail.com
     * @package     Lollipop 
     * @description Class containing usable functions for filesystem
     * 
     */
    class FileSystem
    {
        /**
         * Create or updates file
         *
         * @param   string  $filename   File to create or update
         * @param   string  $contents   Contents to write into file
         * @param   bool    $overwriteExisting  Overwrite existing file
         */
        static function fileWrite($filename, $contents, $overwriteExisting = true) {
            if (file_exists($filename)) {
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
        static function fileRead($filename) {
            if (file_exists($filename)) {
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
        static function fileSize($filename, $returnFormatted = false) {
            if ($returnFormatted) {
                return \Lollipop\Number::readableSize((double)filesize($filename));
            }

            return file_exists($filename) ? filesize($filename) : 0;
        }

        /**
         * Alias unlink
         *
         * @see     unlink();
         * @param   string  Filename of file to be deleted
         */
        static function fileDelete($filename) {
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
        static function fileExists($filename) {
            return file_exists($filename);
        }

        /**
         * Checks if directory exists
         *
         * @param   string  $directory  Directory
         *
         * @return  bool
         */
        static function directoryExists($directory) {
            return is_dir($directory) && is_readable($directory);
        }

        /**
         * Returns the contents of directory
         *
         * @param   string  $directory  Path to directory
         *
         * @return  array
         */
        static function directoryContents($directory) {
            if (!self::directoryExists($directory)) return null;

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
        static function directoryDelete($directory, $force = false) {
            if (self::directoryExists($directory)) {
                /**
                 * Check for files
                 *
                 */
                $contents = scandir($directory);

                if ($force) {
                    foreach ($contents as $content) {
                        if ($content != '.' && $content != '..') {
                            if (self::directoryExists($directory . '/' . $content)) {
                                self::directoryDelete($directory . '/' . $content, true);
                            } else {
                                self::fileDelete($directory . '/' . $content);
                            }
                        }
                    }

                    rmdir($directory);
                } else {
                    $contents = scandir($directory);

                    rmdir($directory);
                }

                if (!self::directoryExists($directory)) return true;
            }

            return false;
        }
    }
?>
