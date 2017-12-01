<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\Text;

/**
 * Session Class 
 *
 * @version     1.2.2
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
     * @access  public
     * @return  void
     * 
     */
    static function start() {
        if (!isset($_SESSION)) session_start();
    }

    /**
     * Stops current session
     *
     * @access  public
     * @return  void
     * 
     */
    static function stop() {
        if (isset($_SESSION)) session_destroy();
    }

    /**
     * Checks if a session variable exists
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @return  bool
     * 
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
     * @access  public
     * @return  string
     * 
     */
    static function key() {
        return md5(Config::get('sugar', Text::lock(SUGAR)));
    }

    /**
     * Creates a new session or sets an existing sesssion
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @param   string  $value  Session variable value
     * @return  string  Session encrypted key
     * 
     */
    static function set($key, $value) {
        self::start();
        
        $key = substr(sha1($key), 0, 10);
        
        $_SESSION[$key] = Text::lock($value, self::key());
        
        return $key;
    }

    /**
     * Gets session variable's value
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @return  string
     * 
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
     * @access  public
     * @param   string  $key    Session variable name
     * @return  string  Deleted encrypted key
     * 
     */
    static function drop($key) {
        self::start();
        
        $key = substr(sha1($key), 0, 10);
        
        if (isset($_SESSION[$key])) unset($_SESSION[$key]);
        
        return $key;
    }
}

?>
