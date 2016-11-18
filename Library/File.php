<?php
    namespace Lollipop;

    /**
     * File Class
     *
     * @version     1.0
     * @author      John Aldrich Bernardo
     * @email       4ldrich@protonmail.com
     * @package     Lollipop 
     * @description Class containing usable functions for file
     * 
     */
    class File
    {
        /**
         * @var string  $_current_file Current filename
         *
         */
        private $_current_file = '';
        
        /**
         * @var bool    $_overwrite_old Overwrite existing file
         *
         */
        private $_overwrite_old = true;

        /**
         * __constructor
         *
         * @param   string  $filename   Filename
         * @param   string  $overwriteExisting  Overwrite existing file
         */
        function __construct($filename, $overwriteExisting = false) {
            $this->_current_file = $filename;
            $this->_overwrite_old = $overwriteExisting;
        }

        /**
         * Sets or gets file contents
         *
         * @param   string  contents    Contents to be put
         *
         * @return  string
         */
        function contents($contents = null) {
            if (is_null($contents))
                return \Lollipop\FileSystem::fileRead($this->_current_file);
            
            if ($this->_overwrite_old) {
                \Lollipop\FileSystem::fileWrite($this->_current_file, $contents);
                return true;
            }
            
            return false;
        }
        
        /**
         * Gets the file size
         *
         * @return  double
         *
         */
        function size() {
            return \Lollipop\FileSystem::fileSize($this->_current_file);
        }
    }
?>
