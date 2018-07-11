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
        
        if (isset($config['environment']))
            self::_setEnvironment();
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
        
        if (!strcasecmp($key, 'environment'))
            self::_setEnvironment();
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
        
        if (!strcasecmp($key, 'environment'))
            self::_setEnvironment();
    }
    
    /**
     * Set environment for application
     *
     * @example
     *          'environment'   =>  'dev' or 'development'
     *          'environment'   =>  'stg' or 'staging'
     *          'environment'   =>  'prd' or 'production'
     *
     * @throws  \Lollipop\Exception\Configuration
     * @return  void
     * 
     */
    static private function _setEnvironment() {
        switch(strtolower(Utils::spare(self::get('environment'), 'dev'))) {
            case 'dev':
            case 'development':
                // Report all errors
                error_reporting(E_ALL);
                break;
            case 'stg':
            case 'staging':
                // Report all errors except E_NOTICE
                error_reporting(E_ALL & ~E_NOTICE);
                break;
            case 'prd':
            case 'production':
                // Turn off error reporting
                error_reporting(0);
                break;
            default:
                throw new \Lollipop\Exception\Configuration('Invalid environment configured');
                break;
        }
        
        /**
         * Modify configuration based on environment
         * 
         * overrides (array)
         * 
         *      dev (array)
         *      stg (array)
         *      prd (array)
         * 
         */
        if (isset(self::$_config['environment']) && 
            isset(self::$_config['overrides']) && 
            isset(self::$_config['overrides'][strtolower(self::$_config['environment'])])) {
            // Merge data
            self::$_config = array_merge(self::$_config, self::$_config['overrides'][strtolower(self::$_config['environment'])]);
        }
    }
}
