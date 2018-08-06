<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Utils;

/**
 * Lollipop Config Class
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop
 *
 */
class Config
{
    /**
     * @type    array   Configuration settings
     *
     */
    static private $_config = [];

    /**
     * Load configuration
     * 
     * @access  public
     * @param   array   $config     Configuration variable
     * @return  void
     * 
     */
    static public function load(array $config) {
        self::$_config = $config;
    }

    /**
     * Add configuration
     * 
     * @access  public
     * @param   string  $key    Configuration key
     * @param   string  $value  Configuration value
     * @return  void
     * 
     */
    static public function add($key, $value) {
        self::set($key, $value);
    }
    
    /**
     * Add or set configuration key
     * 
     * @access  public
     * @param   string  $key    Configuration key
     * @param   string  $value  Configuration value
     * @return  void
     * 
     */
    static public function set($key, $value) {
        $config = &self::$_config;
        
        Utils::arraySet($config, $key, $value);
    }
    
    /**
     * Get configuration
     * 
     * @access  public
     * @param   mixed   $key    Configuration key
     * @param   mixed   $key    Default value for key
     * @return  mixed
     * 
     */
    static public function get($key = null, $default = null) {
        if (is_null($key)) return json_decode(json_encode(self::$_config));
        
        $config = &self::$_config;
        
        return Utils::arrayGet($config, $key, $default);
    }

    /**
     * Remove configuration
     * 
     * @access  public
     * @param   string  $key    Configuration key
     * @return  void
     * 
     */
    static public function remove($key) {
        $config = &self::$_config;
        
        Utils::arrayUnset($config, $key);
    }
}
