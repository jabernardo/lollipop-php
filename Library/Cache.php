<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\Log;

/**
 * Lollipop Caching Library
 *
 * @version     4.2.0
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * 
 */
class Cache
{
    /**
     * @var object  $_driver    Driver Object
     * 
     */
    static private $_driver = null;
    
    /**
     * Get cache driver
     * 
     * @return  object
     * 
     */
    static private function getDriver() {
        if (self::$_driver != null) return self::$_driver;
        
        $driver = spare(Config::get('cache.driver'), 'file');
        
        switch (strtolower($driver)) {
            case 'memcached':
                self::$_driver = new \Lollipop\Cache\Memcached();
                break;
                
            case 'file':
            default:
                self::$_driver = new \Lollipop\Cache\File();
                break;
        }
        
        return self::$_driver;
    }
    
    /**
     * Check if cache exists
     * 
     * @param   string  $key    Cache key
     * @return  bool
     * 
     */
    static function exists($key) {
        return self::getDriver()->exists($key);
    }
    
    /**
     * Save cache
     *
     * @access  public
     * @param   string  $key    Cache key
     * @param   mixed   $data   Data to be saved
     * @param   bool    $force  Force to override old data
     * @param   int     $ttl    Time-to-leave (default to 24 Hrs)
     * @return  bool
     *
     */
    static public function save($key, $data, $force = false, $ttl = 1440) {
        return self::getDriver()->save($key, $data, $force, $ttl);
    }
    
    /**
     * Recover cache
     * 
     * @access  public
     * @param   string  $key    Cache key
     * @return  mixed
     *
     */
    static function recover($key) {
        return self::getDriver()->recover($key);
    }
    
    /**
     * Remove cache
     *
     * @access  public
     * @param   string  $key
     * @return  bool
     *
     */
    static function remove($key) {
        return self::getDriver()->remove($key);
    }
    
    /**
     * Remove all cache
     *
     * @access  public
     * @return  void
     *
     */
    static function purge() {
        return self::getDriver()->purge();
    }
}

?>
