<?php

namespace Lollipop;

/**
 * Url Class
 *
 * @version     1.3
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for URI
 * 
 */
class Url 
{
    /**
     * Get base url
     *
     * @param   string  File or url to access
     * @param   bool    Enable cache buster
     * 
     * @return  string  Base url
     * 
     */
    static function base($url = '', $cacheBuster = false) {
        $cacheb = $cacheBuster ? ('?' . (is_object(\Lollipop\Config::get('app')) && isset(\Lollipop\Config::get('app')->version) ? \Lollipop\Config::get('app')->version : '1.0.0')) : ''; 
        $servern = $_SERVER['SERVER_NAME']; 
        $serverp = $_SERVER['SERVER_PORT'];
        $server = $serverp == '8080' || $serverp == '80' ? $servern : "$servern:$serverp";

        return (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'))
                    ? 'https://' : 'http://') . str_replace('//', '/', ($server . '/' . $url)) . $cacheb;
    }
    
    /**
     * Alias request uri
     *
     * @return  string  Request URI
     *
     */
    static  function here() {
        return self::base($_SERVER['REQUEST_URI']);
    }
    
    /**
     * Is URL alive?
     * 
     * @param   string  $url    URL
     * @return  boolean
     * 
     */
    static function alive($url) {
        return @get_headers($url);
    }
}

?>
