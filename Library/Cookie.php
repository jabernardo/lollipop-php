<?php

namespace Lollipop;

/**
 * Cookie Class
 * 
 * @version     1.0
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class for managing cookies
 */
class Cookie
{
    /**
     * Set cookie
     *
     * @param   string  $key    Cookie name
     * @param   string  $value  Value
     * @param   string  $path   Cookie path
     * @param   long    $expiration     Cookie expiration
     *
     * @return  void
     *
     */
    static function set($key, $value, $path = '/', $expiration = 2592000) {
        setcookie($key, $value, time() + $expiration, $path);
    }

    /**
     * Get cookie
     *
     * @param   string  $key    Cookie name
     *
     * @return  string
     *
     */
    static function get($key) {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }

    /**
     * Check if cookie exists
     *
     * @param   string $key     Cookie name
     *
     * @return  bool
     *
     */
    static function exists($key) {
        return isset($_COOKIE[$key]) ? true : false;
    }

    /**
     * Drop cookie
     *
     * @param   string  $key    Cookie name
     * @param   string  $path   Cookie path
     *
     * @return  void
     *
     */
    static function drop($key, $path = '/') {
        setcookie($key, '', time() - 2650000, $path);
    }

    /**
     * Returns the key used in encrypting session variables
     *
     * @return string
     */
    static function key() {
        return md5(\Lollipop\Text::lock($_SERVER['REMOTE_ADDR'], \Lollipop\App::SUGAR));
    }
}

?>
