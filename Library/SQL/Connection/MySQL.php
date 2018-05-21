<?php

namespace Lollipop\SQL\Connection;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Cache;
use \Lollipop\Config;
use \Lollipop\Log;

/**
 * MySQLi Connection Adapter
 *
 * @package     Lollipop
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * 
 */
class MySQL
{
    use \Lollipop\SQL\BuilderTrait;

    /**
     * Connect to MySQL server
     *
     * @return  void
     *
     */
    private function __connect() {
        $db = Config::get('db');

        if (!is_null($db)) {
            $host = isset($db->host) ?  $db->host : 'localhost';
            $uid = isset($db->username) ?  $db->username : 'root';
            $pwd = isset($db->password) ?  $db->password : '';
            $db = isset($db->database) ?  $db->database : 'lollipop';
                   
            // Instantiate MySQLi
            $this->_mysqli = new \mysqli($host, $uid, $pwd, $db);
            
            if ($this->_mysqli->connect_errno > 0) {
                Log::error($this->_mysqli->connect_error, true);
            }
        } else {
            Log::error('Lollipop is initialized with wrong database configuration', true);
        }
    }

    /**
     * Execute query
     * 
     * @param   bool    $cache  Enable cache (for queries)
     * @return  mixed
     * 
     */
    public function execute($cache = true) {
        // @todo Execute sql here
        if (strlen($this->_sql_query)) {
            // Get cache key
            $cache_key = sha1($this->_sql_query);
            
            // If cache exists and cache is enable
            $config = Config::get('db');
            $cache_enable = isset($config->cache) ? $config->cache : false;
            $cache_time = isset($config->cache_time) ? $config->cache_time : 1440;
            
            if ($cache_enable) {
                if (Cache::exists($cache_key) && $cache) {
                    return Cache::get($cache_key);
                }
            }
            
            // Open connection
            $this->__connect();

            // Execute command
            $return = $this->_mysqli->query($this->_sql_query);

            // Close connection
            $this->_mysqli->close();
            
            // Log executed query
            array_push(self::$_last_commands, $this->_sql_query);
        
            // Return contents
            $results = [];
            
            if (is_object($return) && isset($return->num_rows)) {
                while ($row = $return->fetch_array()) {
                    array_push($results, $row);
                }
                
                // Save cache (overwrites existing)
                if ($cache) {
                    Cache::save($cache_key, $results, true, $cache_time);
                }
                
                return $results;
            }
            
            return $return;
        }
    }
}
