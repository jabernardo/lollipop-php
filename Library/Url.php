<?php
    namespace Lollipop;
    
    /**
     * Url Class
     *
     * @version     1.1
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
            $cacheb = $cacheBuster ? ('?' . (\Lollipop\App::getConfig('app_version') ? \Lollipop\App::getConfig('app_version') : '1.0.0')) : ''; 
            
            return ((@$_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . str_replace('//', '/', ($_SERVER['SERVER_NAME'] . '/' . $url)) . $cacheb;
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
