<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\Log;

/**
 * Lollipop Caching Library
 *
 * @version     4.1.1
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * 
 */
class Cache
{
    /**
     * Checks cache folder
     *
     * @access  private
     * @return  void
     *
     */
    static private function _checkFolder() {
        if (self::_isDriver('sqlite3')) {
            // If driver is sqlite3 check if cache database exists
            self::_initDb();
        }

        if (self::_isDriver('filesystem')) {
            // For `filesystem` check storage path
            if (!is_dir(self::_getStoragePath())) {
                Log::error('Can\'t find app/cache folder', true);
            }
            
            if (!is_writable(self::_getStoragePath())) {
                Log::error('Permission denied for app/cache', true);
            }
        }
    }
    
    /**
     * Storage Path
     * 
     * @access  private
     * @return  string
     * 
     */
    static private function _getStoragePath() {
        if (self::_isDriver('sqlite3')) {
            // Add cache file in path if driver is SQLite3
            return (is_object(Config::get('localdb')) && isset(Config::get('localdb')->folder)) 
                ? rtrim(Config::get('localdb')->folder, '/') . '/cache.db'
                : LOLLIPOP_STORAGE_LOCALDB . 'cache.db';
        }

        return (is_object(Config::get('cache')) && isset(Config::get('cache')->folder)) 
            ? rtrim(Config::get('cache')->folder, '/') . '/' 
            : LOLLIPOP_STORAGE_CACHE;
    }

    /**
     * Check if driver is
     *
     * @param   string  $name   Driver name
     * @return  bool    If driver is currently selected
     *
     */
    static private function _isDriver($name) {
        if (!isset(Config::get('cache')->driver)) {
            Config::set('cache.driver', 'filesystem');
        }

        switch (strtolower(Config::get('cache')->driver)) {
            case 'sqlite3':
            case 'filesystem':
                // Do nothing
                break;
            default:
                Log::error('Invalid cache driver', true);
                break;
        }

        return isset(Config::get('cache')->driver) && Config::get('cache')->driver == $name;
    }

    /**
     * Initialize Database
     *
     * @return  void
     *
     */
    static private function _initDb() {
        // Cache SQL Schema
$sql = <<<EOL
CREATE TABLE IF NOT EXISTS `cache` (
    `id`	INTEGER PRIMARY KEY AUTOINCREMENT,
    `key`	TEXT,
    `ttl`	INTEGER,
    `data`	TEXT,
    `date_created`	TEXT
);
EOL;

        if (!file_exists(self::_getStoragePath())) {
            // If cache doesn't exists, SQLite3 will create it
            $sqlite = new \SQLite3(self::_getStoragePath());

            // Then create table
            $tbl = $sqlite->exec($sql);

            if (!$tbl) {
                Log::error('Error on creating cache table "' . $sqlite->lastErrorMsg() . "'", true);
            }

            $sqlite->close();
        }
    }
    
    /**
     * Check if cache exists
     *
     * @access  public
     * @param   string  $key    Cache key
     * @return  bool
     *
     */
    static public function exists($key) {
        self::_checkFolder();
        
        $key = sha1($key);

        if (self::_isDriver('sqlite3')) {
            $sqlite = new \SQLite3(self::_getStoragePath());

            $time = time(); // Time stamp

            // Select the latest data only, schema is allowed to accept multiple key
            $sql = "SELECT * FROM cache WHERE key = '$key' AND  $time - date_created < ttl LIMIT 1";
            
            $select = $sqlite->query($sql);

            if (!$select) {
                Log::error('Error on finding cache "' . $sqlite->lastErrorMsg() . '"', true);
            }

            $ret = $select->fetchArray(SQLITE3_ASSOC);

            $sqlite->close();

            return $ret ? true : false;
        } else if (self::_isDriver('filesystem')) {
            $fn = self::_getStoragePath() . $key;
            
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

        return false;
    }
    
    /**
     * Save cache
     *
     * @access  public
     * @param   string  $key    Cache key
     * @param   mixed   $data   Data to be saved
     * @param   bool    $force  Force to override old data
     * @param   int     $ttl    Time-to-leave (default to 24 Hrs)
     * @return  void
     *
     */
    static public function save($key, $data, $force = false, $ttl = 1440) {
        self::_checkFolder();
        
        $ttl = $ttl * 60; // Minutes to Seconds
        // Store result from last query for checking if cache is existing
        // This will avoid locked database error for sqlite3
        $cache_exists = self::exists($key);

        if (!$cache_exists || $force) {
            if (self::_isDriver('sqlite3')) {
                $sqlite = new \SQLite3(self::_getStoragePath());
                $date_created = time(); // Timestamp
                $data = $sqlite->escapeString(serialize($data)); // Secure string for storage
                $key_enc = sha1($key); // Generated sha1 key

                $sql = "INSERT INTO cache(key, ttl, data, date_created) VALUES('$key_enc', '$ttl', '$data', '$date_created')";

                if ($force && $cache_exists) {
                    // If cache is already existing, just update the last data
                    $sql = "UPDATE cache SET ttl = '$ttl', data = '$data', date_created = '$date_created' WHERE key = '$key_enc'";
                }

                $q = $sqlite->exec($sql);

                if (!$q) {
                    Log::error('Can\'t add or update cache "' . $sqlite->lastErrorMsg() . '"', true);
                }

                $sqlite->close();
            } else if (self::_isDriver('filesystem')) {
                // Build data into a array
                $data = array(
                    'date_created' => time(),
                    'ttl' => $ttl,
                    'data' => $data
                );
                
                file_put_contents(self::_getStoragePath() . sha1($key), base64_encode(serialize($data)));
            }
        }
    }
    
    /**
     * Recover cache
     * 
     * @access  public
     * @param   string  $key    Cache key
     * @return  mixed
     *
     */
    static public function recover($key) {
        self::_checkFolder();
        
        if (self::exists($key)) {
            $key = sha1($key);

            if (self::_isDriver('sqlite3')) {
                $sqlite = new \SQLite3(self::_getStoragePath());
                $time = time();
                $sql = "SELECT * FROM cache WHERE key = '$key' AND  $time - date_created < ttl LIMIT 1";
                
                $select = $sqlite->query($sql);

                if (!$select) {
                    Log::error('Can\'t recover cache data "' . $sqlite->lastErrorMsg() . '"', true);
                }

                $ret = $select->fetchArray(SQLITE3_ASSOC);

                $sqlite->close();

                return $ret && isset($ret['data']) ? unserialize($ret['data']) : '';
            } else if (self::_isDriver('filesystem')) {
                $contents = file_get_contents(self::_getStoragePath() . $key);
                
                if (base64_decode($contents, true)) {
                    $data = unserialize(base64_decode($contents, true));
                
                    return isset($data['data']) ? $data['data'] : '';
                }
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
    static public function remove($key) {
        self::_checkFolder();
        
        $key = sha1($key);

        if (self::_isDriver('sqlite3')) {
            $sqlite = new \SQLite3(self::_getStoragePath());

            $sql = "DELETE FROM cache WHERE key = '$key'";
            
            $jan = $sqlite->exec($sql);

            if (!$jan) {
                Log::error('Can\'t remove cache "' . $sqlite->lastErrorMsg() . '"', true);
            }

            $sqlite->close();

            return $jan;
        } else if (self::_isDriver('filesystem')) {
            $cache = self::_getStoragePath() . $key;
            
            if (file_exists($cache)) {
                return unlink($cache);
            }
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
    static public function purge() {
        self::_checkFolder();
        
        if (self::_isDriver('sqlite3')) {
            $sqlite = new \SQLite3(self::_getStoragePath());
            // Execute query to delete all cache from cache database
            $sql = "DELETE FROM cache";
            $jan = $sqlite->exec($sql);

            if (!$jan) {
                // Failed to execute sql
                Log::error('Can\'t purge cache data "' . $sqlite->lastErrorMsg() . '"', true);
            }

            $sqlite->close();

            return $jan;
        } else if (self::_isDriver('filesystem')) {
            // Get all files from the cache folder
            $contents = glob(self::_getStoragePath() . '*');

            // Remove cache files
            foreach ($contents as $content) {
                if (is_file($content)) {
                    unlink($content);
                }
            }
        }
    }
}

?>
