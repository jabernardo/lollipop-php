<?php

namespace Lollipop\Cache;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;

/**
 * Lollipop Cache Memcached Library
 * 
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * 
 */
class MemcachedAdapter
{
    /**
     * @var object  $_memcached Memcache library
     * 
     */
    private $_memcached = null;
    
    /**
     * Class construct
     * 
     * @throws  \Lollipop\Exception\Runtime
     * @throws  \Lollipop\Exception\Connection
     * @return  void
     * 
     */
    function __construct() {
        // Check if `Memcached` extension is enabled
        if (!class_exists('\\Memcached')) {
            throw new \Lollipop\Exception\Runtime('`Memcached` extension was not found');
        }
        
        // Get storage path
        $servers = spare(Config::get('cache.servers'), [['127.0.0.1', '11211']]);
        
        // Connect to `Memcached`
        $this->_memcached = new \Memcached();
        $this->_memcached->addServers($servers);
        
        // Test connection
        if (!$this->save('sugar_' . rand(), Config::get('sugar', SUGAR))) {
            throw new \Lollipop\Exception\Connection('Memcached connection failed.');
        }
    }
    
    /**
     * Alias for sha1
     * 
     * @param   string  $key    Key
     * @return  string
     * 
     */
    private function _encrypt($key) {
        return sha1(sha1($key) . Config::get('sugar', SUGAR));
    }
    
    /**
     * Check if cache exists
     *
     * @access  public
     * @param   string  $key    Cache key
     * @return  bool
     *
     */
    public function exists($key) {
        $key = $this->_encrypt($key);
        $data = $this->_memcached->get($key);
        
        return $data ? true : false;
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
    public function save($key, $data, $force = false, $ttl = 1440) {
        $ttl = $ttl * 60; // Minutes to Seconds
        // Store result from last query for checking if cache is existing
        // This will avoid locked database error for sqlite3
        $cache_exists = $this->exists($key);

        if (!$cache_exists || $force) {
            $key = $this->_encrypt($key);
            $this->_memcached->set($key, $data, $ttl);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get cache
     * 
     * @access  public
     * @param   string  $key    Cache key
     * @return  mixed
     *
     */
    public function get($key) {
        return $this->_memcached->get($this->_encrypt($key));
    }
    
    /**
     * Remove cache
     *
     * @access  public
     * @param   string  $key
     * @return  bool
     *
     */
    public function remove($key) {
        if ($this->exists($key)) {
            $key = $this->_encrypt($key);
            $this->_memcached->delete($key);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Remove all cache
     *
     * @access  public
     * @return  void
     *
     */
    public function purge() {
        $this->_memcached->flush();
    }
}
