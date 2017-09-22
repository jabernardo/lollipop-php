<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\FileSystem;

/**
 * File Class
 *
 * @version     1.2
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
     * @var bool    $_is_temp   Is temporary file?
     * 
     */
    private $_is_temp = false;

    /**
     * __constructor
     *
     * @param   string  $filename   Filename
     * @param   string  $overwriteExisting  Overwrite existing file
     * @return  object
     * 
     */
    function __construct($filename, $overwriteExisting = false) {
        $this->_current_file = $filename;
        $this->_overwrite_old = $overwriteExisting;
        
        return $this;
    }
    
    /**
     * __deconstruct
     * 
     * @access  public
     * @return  void
     * 
     */
    function __destruct() {
        if ($this->_is_temp) {
            $this->delete();
        }
    }

    /**
     * Sets or gets file contents
     *
     * @access  public
     * @param   string  contents    Contents to be put
     * @return  string
     */
    function contents($contents = null) {
        if (is_null($contents))
            return FileSystem::fileRead($this->_current_file);
        
        if ($this->_overwrite_old) {
            FileSystem::fileWrite($this->_current_file, $contents);
            return true;
        }
        
        return false;
    }
    
    /**
     * Set file as temporary
     * Setting this will delete file on deconstruct
     * 
     * @access  public
     * @return  object
     * 
     */
    function temp() {
        $this->_is_temp = true;
        
        return $this;
    }
    
    /**
     * Gets the file size
     *
     * @access  public
     * @return  double
     *
     */
    function size() {
        return FileSystem::fileSize($this->_current_file);
    }
    
    /**
     * Delete file
     * 
     * @access  public
     * @return  bool
     * 
     */
    function delete() {
        return FileSystem::fileDelete($this->_current_file);
    }
}

?>
