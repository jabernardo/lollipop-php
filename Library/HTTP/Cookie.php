<?php

namespace Lollipop\HTTP;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Text;

/**
 * Cookie Class
 * 
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
    static function set($name, $value = "", $expire = 0, $path = "", $domain = "", $secure = false, $httponly = false) {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
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
     * Get all cookies
     *
     * @return array
     */
    static function getAll() {
        return $_COOKIE;
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
    static function drop($name, $path = "", $domain = "") {
        setcookie($name, '', time() - 2650000, $path, $domain);
    }
}
