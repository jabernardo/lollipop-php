<?php

namespace Lollipop\HTTP;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;

/**
 * Url Class
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for URI
 * 
 */
class URL 
{
    /**
     * Get base url
     *
     * @access  public
     * @param   string  File or url to access
     * @param   bool    Enable cache buster
     * @return  string  Base url
     * 
     */
    static function base($url = '', $cacheBuster = false) {
        $cacheb = $cacheBuster ? ('?' . (Config::get('app.version', '1.0.0'))) : ''; 
        $servern = $_SERVER['SERVER_NAME']; 
        $serverp = $_SERVER['SERVER_PORT'];
        $server = $serverp == '8080' || $serverp == '80' || $serverp == '443' ? $servern : "$servern:$serverp";

        return (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'))
                    ? 'https://' : 'http://') . str_replace('//', '/', ($server . '/' . $url)) . $cacheb;
    }
    
    /**
     * Alias request uri
     *
     * @access  public
     * @return  string  Request URI
     *
     */
    static  function here() {
        return self::base($_SERVER['REQUEST_URI']);
    }
    
    /**
     * Is URL alive?
     * 
     * @access  public
     * @param   string  $url    URL
     * @return  boolean
     * 
     */
    static function alive($url) {
        return @get_headers($url);
    }
    
    /**
     * Reloads current page
     * 
     * @access  public
     * @return  void
     * 
     */
    static function reload() {
        header('location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }
    
    /**
     * Redirect page to another urldecode
     *
     * @access  public
     * @param   string     $uri    Web address
     * @throws  \Lollipop\Exception\Argument
     * @return  void
     * 
     */
    static function redirect($uri) {
        // Check first if given string is a valid URL 
        if (!filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new \Lollipop\Exception\Argument('URL is invalid', true);
        }
        
        header('location: ' . $uri);
        exit();
    }
}
