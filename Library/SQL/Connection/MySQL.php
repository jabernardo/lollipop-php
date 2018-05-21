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
class MySQL implements \Lollipop\SQL\ConnectionInterface
{
    use \Lollipop\SQL\BuilderTrait;

    /**
     * Database object
     *
     * @var object
     */
    private $db;

    /**
     * Connect to MySQL server
     *
     * @return  boolean
     *
     */
    private function __connect() {
        if (!is_null($db)) return true;

        $config = Config::get('db');

        if (is_null($config)) {
            Log::error('Lollipop is initialized with wrong database configuration', true);
        }

        $host = isset($config->host) ?  $config->host : 'localhost';
        $uid = isset($config->username) ?  $config->username : 'root';
        $pwd = isset($config->password) ?  $config->password : '';
        $db = isset($config->database) ?  $config->database : 'lollipop';
        
        // Instantiate MySQLi
        $this->db = new \mysqli($host, $uid, $pwd, $db);
        
        if ($this->db->connect_errno > 0) {
            Log::error($this->db->connect_error, true);
        }

        return true;
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
            
            // Return contents
            $results = [];

            // Open connection
            $this->__connect();

            // Execute command
            $return = $this->db->query($this->_sql_query);

            // Close connection
            $this->db->close();
            
            // Log executed query
            array_push(self::$_last_commands, $this->_sql_query);
        
            
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
