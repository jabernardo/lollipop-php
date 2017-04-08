<?php

namespace Lollipop;

use \Lollipop\App;
use \Lollipop\Config;
use \Lollipop\Cookie;
use \Lollipop\Request;
use \Lollipop\Route;
use \Lollipop\Text;

/**
 * Csrf Token Class
 *
 * @version     1.2
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
     * Get token name
     * 
     * @access  public
     * @return  string
     * 
     */
    public static function getName() {
        return Config::get('anti_csrf') && isset(Config::get('anti_csrf')->name) && Config::get('anti_csrf')->name ? Config::get('anti_csrf')->name : 'sugar';
    }
    
    /**
     * Get Form input
     * 
     * @access  public
     * @return  string
     * 
     */
    public static function getFormInput() {
        $name = Config::get('anti_csrf') && isset(Config::get('anti_csrf')->name) && Config::get('anti_csrf')->name ? Config::get('anti_csrf')->name : 'sugar';
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
        $expiration = Config::get('anti_csrf') && isset(Config::get('anti_csrf')->expiration) && Config::get('anti_csrf')->expiration ? Config::get('anti_csrf')->expiration : 1800;
        
        // Create a cookie for front end use
        Cookie::set($acsrf_name, self::get(), '/', $expiration);
        
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
        $net = '<!DOCTYPE html>';
        $net .= '<!-- Lollipop for PHP by John Aldrich Bernardo -->';
        $net .= '<html>';
        $net .= '<head><title>Not Enough Tokens</title></head>';
        $net .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $net .= '<body>';
        $net .= '<h1>Not Enough Tokens</h1>';
        $net .= '<p>Oops! Make sure you have enough tokens before you can play.</p>';
        $net .= '</body>';
        $net .= '</html>';
        
        $output = $net;
        $output_config = Config::get('output');
        $output_compression = !is_null($output_config) && isset($output_config->compression) && $output_config->compression;
        
        if ($output_compression) {
            // Set Content coding a gzip
            Route::setHeader('Content-Encoding: gzip');
            
            // Set headers for gzip
            $output = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $output .= gzcompress($net);
        }
        
        echo $output;
        
        exit;
    }
}

?>
