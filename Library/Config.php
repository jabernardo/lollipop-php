<?php

namespace Lollipop;

/**
 * Lollipop Config Class
 *
 * @version     2.0
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
    }
    
    /**
     * Get configuration
     * 
     * @access  public
     * @param   string  $key    Configuration key
     * @return  mixed
     * 
     */
    static public function get($key = null) {
        if (is_null($key)) return json_decode(json_encode(self::$_config));
        
        $toks = explode('.', $key);
        $addr = &self::$_config;
        
        for ($i = 0; $i < count($toks); $i++) {
            $addr = &$addr[$toks[$i]];
        }
        
        return is_array($addr) || is_object($addr) ? json_decode(json_encode($addr)) : $addr;
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
    }
}

?>
