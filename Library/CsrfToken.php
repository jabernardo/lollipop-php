<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\Text;

/**
 * Csrf Token Class
 *
 * @version     1.2.5
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Anti Csrf Token verification class
 * 
 */
class CsrfToken
{
    /**
     * Get a token
     * 
     * @access  public
     * @return  string  Random Token
     * 
     */
    public static function get() {
        return Text::lock(microtime(true), self::getKey());
    }
    
    /**
     * Get token key
     * 
     * @access  public
     * @return  string
     * 
     */
    public static function getKey() {
        return Config::get('anti_csrf.key', SUGAR);
    }
    
    /**
     * Get token name
     * 
     * @access  public
     * @return  string
     * 
     */
    public static function getName() {
        return Config::get('anti_csrf.name', 'sugar');
    }
    
    /**
     * Get Form input
     * 
     * @access  public
     * @return  string
     * 
     */
    public static function getFormInput() {
        $name = self::getName();
        $value = self::get();
        
        return "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
    }
    
    /**
     * Check Validity of Token
     * 
     * @access  public
     * @return  bool
     * 
     */
    public static function isValid($token) {
        // Get configuration
        $expiration = Config::get('anti_csrf.expiration', 18000);
        // Compute for token availablity
        $computed = microtime(true) - (double)Text::unlock($token, self::getKey());
        
        return $computed <= $expiration;
    }
}

?>
