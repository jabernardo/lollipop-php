<?php

namespace Lollipop;

use \Lollipop\App;
use \Lollipop\Text;

/**
 * Session Class 
 *
 * @version     1.1
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for a secured session
 */
class Session
{
    /**
     * Starts session
     *
     */
    static function start() {
        if (!isset($_SESSION)) session_start();
    }

    /**
     * Stops current session
     *
     */
    static function stop() {
        if (isset($_SESSION)) session_destroy();
    }

    /**
     * Checks if a session variable exists
     *
     * @param   string  $key    Session variable name
     * 
     * @return  bool
     */
    static function exists($key) {
        self::start();
        
        $key = substr(sha1($key), 0, 10);
        
        if (isset($_SESSION[$key])) return true;
        
        return false;
    }

    /**
     * Returns the key used in encrypting session variables
     *
     * @return string
     */
    static function key() {
        return md5(Text::lock(App::SUGAR));
    }

    /**
     * Creates a new session or sets an existing sesssion
     *
     * @param   string  $key    Session variable name
     * @param   string  $value  Session variable value
     */
    static function set($key, $value) {
        self::start();
        
        $key = substr(sha1($key), 0, 10);
        
        $_SESSION[$key] = Text::lock($value, self::key());
    }

    /**
     * Gets session variable's value
     *
     * @param   string  $key    Session variable name
     * 
     * @return  string
     */
    static function get($key) {
        self::start();
        
        $key = substr(sha1($key), 0, 10);
        
        if (isset($_SESSION[$key])) {
            return trim(Text::unlock($_SESSION[$key], self::key()));
        } else {
            return '';
        }
    }

    /**
     * Removes a session variable
     *
     * @param   string  $key    Session variable name
     */
    static function drop($key) {
        self::start();
        
        $key = substr(sha1($key), 0, 10);
        
        if (isset($_SESSION[$key])) unset($_SESSION[$key]);
    }
}

?>
