<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

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
        $toks = explode('.', $key);
        $addr = &self::$_config;
        
        for ($i = 0; $i < count($toks); $i++) {
            $addr = &$addr[$toks[$i]];
        }
        
        $addr = $value;
        
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
        
        $toks = explode('.', $key);
        $addr = &self::$_config;
        
        for ($i = 0; $i < count($toks); $i++) {
            $addr = &$addr[$toks[$i]];
        }
        
        return is_array($addr) || is_object($addr)
                    ? json_decode(json_encode($addr))
                    : (is_null($addr) && !is_null($default) ? $default : $addr) ;
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
        $toks = explode('.', $key);
        $toks_len = count($toks);
        $addr = &self::$_config;
        $last = null;

        for ($i = 0; $i < $toks_len - 1; $i++) {
            $addr = &$addr[$toks[$i]];
        }

        if (isset($toks[$toks_len - 1])) {
            $last = $toks[$toks_len - 1];
        }

        if ($last) unset($addr[$last]);
        
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
     * @return  void
     *
     */
    static private function _setEnvironment() {
        switch(strtolower(spare(self::get('environment'), 'dev'))) {
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
                Log::error('Invalid application environment: ' . self::get('environment'), true);
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
