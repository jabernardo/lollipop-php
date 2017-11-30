<?php

namespace Lollipop\Cache;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\Log;

/**
 * Lollipop Cache File Library
 *
 * @version     1.0.0
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * 
 */
class File
{
    /**
     * @var     string  $_storage_path  Cache storage path
     * 
     */
    private $_storage_path = LOLLIPOP_STORAGE_CACHE;
    
    /**
     * Class construct
     * 
     * @return  void
     * 
     */
    function __construct() {
        // Get storage path
        $this->_storage_path = Config::get('cache.folder')
            ? rtrim(Config::get('cache.folder'), '/') . '/' 
            : LOLLIPOP_STORAGE_CACHE;
        
        // For `filesystem` check storage path
        if (!is_dir($this->_storage_path)) {
            Log::error('Can\'t find app/cache folder', true);
        }
        
        if (!is_writable($this->_storage_path)) {
            Log::error('Permission denied for app/cache', true);
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
        return sha1($key);
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
        $fn = $this->_storage_path . $key;
       
        // Cache janitor for filesystem will be moved here
        if (file_exists($fn)) {
            $contents = file_get_contents($fn);
            
            if (!base64_decode($contents, true)) {
                unlink($fn);
                return;
            }

            $data = unserialize(base64_decode($contents, true));
            // Will check for expiration
            if (isset($data['date_created']) && isset($data['ttl']) && isset($data['data'])) {
                if (time() - (int)$data['date_created'] >= $data['ttl']) {
                    unlink($fn);
                }
            } else {
                unlink($fn);
            }
        }

        return file_exists($fn);
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
            // Build data into a array
            $data = array(
                'date_created' => time(),
                'ttl' => $ttl,
                'data' => $data
            );
            
            file_put_contents($this->_storage_path . $this->_encrypt($key), base64_encode(serialize($data)));
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Recover cache
     * 
     * @access  public
     * @param   string  $key    Cache key
     * @return  mixed
     *
     */
    public function recover($key) {
        if ($this->exists($key)) {
            $key = $this->_encrypt($key);
            $contents = file_get_contents($this->_storage_path . $key);
            
            if (base64_decode($contents, true)) {
                $data = unserialize(base64_decode($contents, true));
            
                return isset($data['data']) ? $data['data'] : '';
            }
        }
        
        return '';
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
        $key = $this->_encrypt($key);
        $cache = $this->_storage_path . $key;
        
        if (file_exists($cache)) {
            return unlink($cache);
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
        // Get all files from the cache folder
        $contents = glob($this->_storage_path . '*');

        // Remove cache files
        foreach ($contents as $content) {
            if (is_file($content)) {
                unlink($content);
            }
        }
    }
}

?>
