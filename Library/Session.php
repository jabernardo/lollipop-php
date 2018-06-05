<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;

/**
 * Lollipop Session Library
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * 
 */
class Session
{
    /**
     * @var object  $_driver    Driver Object
     * 
     */
    static private $_driver = null;

    /**
     * Get session driver
     * 
     * @return  object
     * 
     */
    static private function getDriver() {
        if (self::$_driver != null) return self::$_driver;
        
        $driver = spare(Config::get('session.driver'), 'default');

        switch (strtolower($driver)) {
            case 'default':
            default:
                self::$_driver = new \Lollipop\Session\Session();
                break;
        }
        
        return self::$_driver;
    }

    /**
     * Reload session driver
     *
     * @return void
     */
    static function reload() {
        // Reload session driver
        self::$_driver = null;
    }
    
    /**
     * Check if session exists
     * 
     * @param   string  $key    Cache key
     * @return  bool
     * 
     */
    static function exists($key) {
        return self::getDriver()->exists($key);
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
        return self::getDriver()->set($key, $value);
    }
    
    /**
     * Get session
     * 
     * @access  public
     * @param   string  $key    Cache key
     * @return  mixed
     *
     */
    static function get($key) {
        return self::getDriver()->get($key);
    }
    
    /**
     * Get session id
     * 
     * @access  public
     * @return  string
     * 
     */
    static function getId() {
        return self::getDriver()->getId();
    }
    
    /**
     * Get all session variables
     * 
     * @access  public
     * @return  array
     */
    static function getAll() {
        return self::getDriver()->getAll();
    }
    
    /**
     * Removes a session variable
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @return  string  Deleted encrypted key
     * 
     */
    static function remove($key) {
        return self::getDriver()->remove($key);
    }
    
    /**
     * Remove all registered session variables
     * 
     * @return  bool
     * 
     */
    static function removeAll() {
        return self::getDriver()->removeAll();
    }
}
