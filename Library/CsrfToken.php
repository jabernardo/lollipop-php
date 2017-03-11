<?php

namespace Lollipop;

use \Lollipop\App;
use \Lollipop\Config;
use \Lollipop\Cookie;
use \Lollipop\Request;
use \Lollipop\Text;

/**
 * Csrf Token Class
 *
 * @version     1.0
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
        $key = Config::get('anti_csrf') && isset(Config::get('anti_csrf')->key) && Config::get('anti_csrf')->key ? Config::get('anti_csrf')->key : App::SUGAR;
        
        return Text::lock(microtime(true), $key);
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
        $key = Config::get('anti_csrf') && isset(Config::get('anti_csrf')->key) && Config::get('anti_csrf')->key ? Config::get('anti_csrf')->key : App::SUGAR;
        $expiration = Config::get('anti_csrf') && isset(Config::get('anti_csrf')->expiration) && Config::get('anti_csrf')->expiration ? Config::get('anti_csrf')->expiration : 1800;
        // Compute for token availablity
        $computed = microtime(true) - (double)Text::unlock($token, $key);
        
        return $computed <= $expiration;
    }
    
    /**
     * Hook or verify 
     * 
     * @access  public
     * @param   bool    $die    Die on failure
     * @return  mixed
     * 
     */
    public static function hook($die = true) {
        $acsrf_enable = Config::get('anti_csrf') && isset(Config::get('anti_csrf')->enable) ? Config::get('anti_csrf')->enable : false;
        $acsrf_name = Config::get('anti_csrf') && isset(Config::get('anti_csrf')->name) ? Config::get('anti_csrf')->name : 'sugar';
        
        // Create a cookie for front end use
        Cookie::set($acsrf_name, self::get(), '/', 1800);
        
        // Validate cookie
        if (Request::get() && $acsrf_enable) {
            if (!Request::get($acsrf_name) || !self::isValid(Request::get($acsrf_name))) {
                if ($die) {
                    self::_kill();
                }
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Self killed
     * 
     * 
     */
    private static function _kill() {
        echo '<!DOCTYPE html>';
        echo '<!-- Lollipop for PHP by John Aldrich Bernardo -->';
        echo '<html>';
        echo '<head><title>Not Enough Tokens</title></head>';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<body>';
        echo '<h1>Not Enough Tokens</h1>';
        echo '<p>Oops! Make sure you have enough tokens before you can play.</p>';
        echo '</body>';
        echo '</html>';
        exit;
    }
}

?>
