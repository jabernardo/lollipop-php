<?php

namespace Lollipop;

/**
 * Lollipop Config Class
 *
 * @version     2.0-rc1-dev
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
    static private $_config = array();

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
    
    // @todo validate
    static public function set($key, $value) {
        $toks = explode('.', $key);
        $addr = &self::$_config;
        
        for ($i = 0; $i < count($toks); $i++) {
            $addr = &$addr[$toks[$i]];
        }
        
        $addr = $value;
    }
    
    // @todo change all old usage of config
    static public function get($key) {
        $toks = explode('.', $key);
        $addr = &self::$_config;
        
        for ($i = 0; $i < count($toks); $i++) {
            $addr = &$addr[$toks[$i]];
        }
        
        return is_array($addr) || is_object($addr) ? json_decode(json_encode($addr)) : $addr;
    }
    
    /**
     * Get configuration
     * 
     * @access  public
     * @param   string  $key    Configuration key
     * @return  mixed
     * 
     */
    //static public function get($key = '') {
    //    return $key ? (isset(self::$_config[$key]) ? (is_array(self::$_config[$key]) ? (object)self::$_config[$key] : self::$_config[$key]) : null) : self::$_config;
    //}
    
    /**
     * Remove configuration
     * 
     * @access  public
     * @param   string  $key    Configuration key
     * @return  void
     * 
     */
    static public function remove($key) {
        unset(self::$_config[$key]);
    }
}

?>
