<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Cache;
use \Lollipop\Config;
use \Lollipop\Log;

/**
 * Database Driver for MySQLi
 *
 * @package     Lollipop
 * @version     2.8.4
 * @uses        \Lollipop\Cache
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @description MySQLi Database Adapter
 * 
 */
class Database
{
    /**
     * MySQL instance
     * 
     * @type object
     * 
     */
    private $_mysqli = null;
    
    /**
     * Selected table
     * 
     * @type string
     */
    private $_table = '';
    
    /**
     * Alias
     * 
     * @type string
     * 
     */
    private $_alias  = '';
    
    /**
     * Updates
     * 
     * @type array
     * 
     */
    private $_updates = [];
    
    /**
     * Fields selected
     * 
     * @type string
     */
    private $_fields = '';
    
    /**
     * Values for insert
     * 
     * @type string
     */
    private $_values = '';
    
    /**
     *  Where statements
     * 
     * @type array
     * 
     */
    private $_where = [];
    
    /**
     * Or statements
     * 
     * @type array
     * 
     */
    private $_or = [];
    
    /**
     * Select distinct only
     * 
     * @type string
     */
    private $_distinct = '';
    
    /**
     * Joins
     * 
     * @type array
     * 
     */
    private $_joins = [];
    
    /**
     * Unions
     * 
     * @type array
     * 
     */
    private $_union = [];
    
    /**
     * Union All
     * 
     * @type array
     * 
     */
    private $_union_all = [];
    
    /**
     * Group by
     * 
     * @type string
     * 
     */
    private $_group_by = '';
    
    /**
     * Order by
     * 
     * @type string
     * 
     */
    private $_order = '';
    
    /**
     * Limit
     * 
     * @type string
     * 
     */
    private $_limit = '';
    
    /**
     * Built SQL Query
     * 
     * @type string
     * 
     */
    private $_sql_query = '';
    
    /**
     * Last executed command
     * 
     * @type array
     * 
     */
    private static $_last_commands = [];
    
    
    /**
     * To string magic function
     * 
     * @return  string
     * 
     */
    public function __toString() {
        return $this->_sql_query;
    }
    
    /**
     * Select table
     * 
     * @param   string  $table
     * @param   bool    $isQuery
     * 
     */
    public static function table($table, $isQuery = false) {
        $new_self = new self();
        
        if ($isQuery) {
            $new_self->_table = '(' . $table . ')';
        } else {
            $new_self->_table = $table;
        }

        return $new_self;
    }
    
    /**
     * Set alias for query
     * 
     * @param 
     * 
     */
    public function alias($alias) {
        $this->_alias = $alias;
        
        return $this;
    }
    
    /**
     * Select fields
     * 
     * @param   array   $fields     Fields to select
     * 
     * @return  array 
     * 
     */
    public function select($fields) {
        if (is_array($fields)) {
            $this->_fields = implode($fields, ', ');
        } else {
            $this->_fields = $fields;
        }
        
        // Build Select Command
        $sql_query = 'SELECT ' . $this->_distinct . $this->_fields .
                     ' FROM ' . $this->_table;

        if (count($this->_joins)) {
            $sql_query .= implode($this->_joins, ' ');
        }
        
        // Where statements
        if (count($this->_where)) {
            $sql_query .= ' WHERE ';
            $sql_query .= implode($this->_where, ' AND ');
            
            // or statements
            if (count($this->_or)) {
                $sql_query .= ' OR ';
                $sql_query .= implode($this->_or, ' AND ');
            }
        }
        
        // Alias
        if ($this->_alias) {
            $sql_query .= ' AS ' . $this->_alias;
        }
        
        // Union
        if (count($this->_union)) {
            $sql_query .= ' UNION ' . implode($this->_union, ' UNION ');
        }
        
        // Union All
        if (count($this->_union_all)) {
            $sql_query .= ' UNION ALL ' . implode($this->_union_all, ' UNION ALL ');
        }
        
        // Group By
        if (count($this->_group_by)) {
            $sql_query .= $this->_group_by;
        }
        
        // Order By
        if (count($this->_order)) {
            $sql_query .= $this->_order;
        }
        
        // Limit
        if (count($this->_limit)) {
            $sql_query .= $this->_limit;
        }
        
        // Set the query
        $this->_sql_query = $sql_query;
        
        return $this;
    }
    
    /**
     * Select fields
     * 
     * @return  array
     * 
     */
    public function selectAll() {
        // Build Select Command
        $sql_query = 'SELECT ' . $this->_distinct .
                     '* FROM ' . $this->_table;

        if (count($this->_joins)) {
            $sql_query .= implode($this->_joins, ' ');
        }
        
        // Where statements
        if (count($this->_where)) {
            $sql_query .= ' WHERE ';
            $sql_query .= implode($this->_where, ' AND ');
            
            // or statements
            if (count($this->_or)) {
                $sql_query .= ' OR ';
                $sql_query .= implode($this->_or, ' AND ');
            }
        }
        
        // Alias
        if ($this->_alias) {
            $sql_query .= ' AS ' . $this->_alias;
        }
        
        // Union
        if (count($this->_union)) {
            $sql_query .= ' UNION ' . implode($this->_union, ' UNION ');
        }
        
        // Union All
        if (count($this->_union_all)) {
            $sql_query .= ' UNION ALL ' . implode($this->_union_all, ' UNION ALL ');
        }
        
        // Group By
        if (count($this->_group_by)) {
            $sql_query .= $this->_group_by;
        }
        
        // Order By
        if (count($this->_order)) {
            $sql_query .= $this->_order;
        }
        
        // Limit
        if (count($this->_limit)) {
            $sql_query .= $this->_limit;
        }
        
        // Set the query
        $this->_sql_query = $sql_query;
        
        return $this;
    }
    
    /**
     * Insert data to table
     * 
     * @param   array   $fields Fields to put in data
     * 
     * @return  mixed   Returns $this when insert succeed and false on failure
     * 
     */
    public function insert($fields) {
        // Create temporary variables for 
        // tokenized parameter
        $_tmp_fields = [];
        $_tmp_values = [];
        
        if (is_array($fields)) {
            foreach ($fields as $key => $value) {
                array_push($_tmp_fields, $key);
                
                if (is_string($value)) {
                    array_push($_tmp_values, '\'' . addslashes($value) . '\'');
                } else {
                    array_push($_tmp_values, $value);
                }
            }
            
            $this->_fields = implode($_tmp_fields, ', ');
            $this->_values = implode($_tmp_values, ', ');
        } else {
            return false;
        }
        
        $sql_query = 'INSERT INTO ' . $this->_table . 
                     '(' . $this->_fields . ')' .
                     ' VALUES(' . $this->_values . ')';
                     
        // Set the query
        $this->_sql_query = $sql_query;
        
        // Execute query
        return $this;
    }
    
    /**
     * Delete data from table
     * 
     * @return  mixed   Returns $this when insert succeed and false on failure
     * 
     */
    public function remove() {
        if (is_array($this->_fields)) {
            $this->_fields = implode($fields, ', ');
        }
        
        // Build Select Command
        $sql_query = 'DELETE FROM ' . $this->_table;
        
        // Where statements
        if (count($this->_where)) {
            $sql_query .= ' WHERE ';
            $sql_query .= implode($this->_where, ' AND ');
            
            // or statements
            if (count($this->_or)) {
                $sql_query .= ' OR ';
                $sql_query .= implode($this->_or, ' AND ');
            }
        }
        
        // Set the query
        $this->_sql_query = $sql_query;
        
        return $this;
    }
    
    /**
     * Update data from table
     * 
     * @param   array   $fields Fields to put in data
     * 
     * @return  mixed   Returns $this when insert succeed and false on failure
     * 
     */
    public function update($fields) {
        if (is_array($fields)) {
            foreach ($fields as $key => $value) {
                if (is_string($value)) {
                    array_push($this->_updates, $key . ' = \'' . addslashes($value) . '\'');
                } else {
                    array_push($this->_updates, $key . ' = ' . $value);
                }
            }
        } else {
            return false;
        }
        
        $sql_query = 'UPDATE ' . $this->_table . 
                     ' SET ' . implode($this->_updates, ', ');
        
        // Where statements
        if (count($this->_where)) {
            $sql_query .= ' WHERE ';
            $sql_query .= implode($this->_where, ' AND ');
            
            // or statements
            if (count($this->_or)) {
                $sql_query .= ' OR ';
                $sql_query .= implode($this->_or, ' AND ');
            }
        }
        
        // Set the query
        $this->_sql_query = $sql_query;
        
        return $this;
    }

    /**
     * Increment value
     * 
     * @param   string  $field  Field to increment
     * @param   double  $val    Increment value
     * 
     * @return  mixed   Returns $this when insert succeed and false on failure
     * 
     */
    public function increment($field, $val) {
        if (!is_numeric($val)) return false;

        $sql_query = 'UPDATE ' . $this->_table . 
                     ' SET ' . $field . ' = ' . $field . ' + ' . (double)$val;
        
        // Where statements
        if (count($this->_where)) {
            $sql_query .= ' WHERE ';
            $sql_query .= implode($this->_where, ' AND ');
            
            // or statements
            if (count($this->_or)) {
                $sql_query .= ' OR ';
                $sql_query .= implode($this->_or, ' AND ');
            }
        }
        
        // Set the query
        $this->_sql_query = $sql_query;
        
        return $this;
    }

    /**
     * Decrement value
     * 
     * @param   string  $field  Field to decrement
     * @param   double  $val    Decrement value
     * 
     * @return  mixed   Returns $this when insert succeed and false on failure
     * 
     */
    public function decrement($field, $val) {
        if (!is_numeric($val)) return false;
        
        $sql_query = 'UPDATE ' . $this->_table . 
                     ' SET ' . $field . ' = ' . $field . ' - ' . (double)$val;
        
        // Where statements
        if (count($this->_where)) {
            $sql_query .= ' WHERE ';
            $sql_query .= implode($this->_where, ' AND ');
            
            // or statements
            if (count($this->_or)) {
                $sql_query .= ' OR ';
                $sql_query .= implode($this->_or, ' AND ');
            }
        }
        
        // Set the query
        $this->_sql_query = $sql_query;
        
        return $this;
    }
    
    /**
     * Sum rows in field/s
     * 
     * @param   array   Field/s name
     * 
     */
    public function sum($field) {
        // Temporary key sum strings
        $_tmp_sum = [];
        
        if (is_array($field)) {
            foreach($field as $key => $value) {
                // Multiple fields
                array_push($_tmp_sum, 'SUM(' . ($key ? $key : $value) . ') ' . ($key ? ' AS ' . $value : ''));
            }
        } else {
            // Single field
            array_push($_tmp_sum, 'SUM(' . $field . ')');
        }
        
        $sql_query = 'SELECT ' . implode($_tmp_sum, ', ') .
                     ' FROM ' . $this->_table;
                     
        // Where statements
        if (count($this->_where)) {
            $sql_query .= ' WHERE ';
            $sql_query .= implode($this->_where, ' AND ');
            
            // or statements
            if (count($this->_or)) {
                $sql_query .= ' OR ';
                $sql_query .= implode($this->_or, ' AND ');
            }
        }
        
        // Set the query
        $this->_sql_query = $sql_query;
        
        return $this;
    }
    
    /**
     * Union 
     * 
     * @param   string  $sql    SQL string
     * 
     */
    public function union($sql) {
        array_push($this->_union, $sql);
        
        return $this;
    }
    
    /**
     * Union All
     * 
     * @param   string  $sql    SQL string
     * 
     */
    public function unionAll($sql) {
        array_push($this->_union_all, $sql);
        
        return $this;
    }
    
    /**
     * Count rows in field/s
     * 
     * @param   array   Field/s name
     * 
     */
    public function count($field) {
        // Temporary key sum strings
        $_tmp_sum = [];
        
        if (is_array($field)) {
            foreach($field as $key => $value) {
                // Multiple fields
                array_push($_tmp_sum, 'COUNT(' . ($key ? $key : $value) . ') ' . ($key ? ' AS ' . $value : ''));
            }
        } else {
            // Single field
            array_push($_tmp_sum, 'COUNT(' . $field . ')');
        }
        
        $sql_query = 'SELECT ' . implode($_tmp_sum, ', ') .
                     ' FROM ' . $this->_table;
                     
        // Where statements
        if (count($this->_where)) {
            $sql_query .= ' WHERE ';
            $sql_query .= implode($this->_where, ' AND ');
            
            // or statements
            if (count($this->_or)) {
                $sql_query .= ' OR ';
                $sql_query .= implode($this->_or, ' AND ');
            }
        }
        
        // Set the query
        $this->_sql_query = $sql_query;
        
        return $this;
    }
    
    /**
     * Get max field/s value
     * 
     * @param   array   Field/s name
     * 
     */
    public function max($field) {
        // Temporary key sum strings
        $_tmp_sum = [];
        
        if (is_array($field)) {
            foreach($field as $key => $value) {
                // Multiple fields
                array_push($_tmp_sum, 'MAX(' . ($key ? $key : $value) . ') ' . ($key ? ' AS ' . $value : ''));
            }
        } else {
            // Single field
            array_push($_tmp_sum, 'MAX(' . $field . ')');
        }
        
        $sql_query = 'SELECT ' . implode($_tmp_sum, ', ') .
                     ' FROM ' . $this->_table;
                     
        // Where statements
        if (count($this->_where)) {
            $sql_query .= ' WHERE ';
            $sql_query .= implode($this->_where, ' AND ');
            
            // or statements
            if (count($this->_or)) {
                $sql_query .= ' OR ';
                $sql_query .= implode($this->_or, ' AND ');
            }
        }
        
        // Set the query
        $this->_sql_query = $sql_query;
        
        return $this;
    }
    
    /**
     * Where statements
     * 
     * @param   string  $field Table field
     * @param   mixed   $operator   Comparer
     * @param   mixed   $value  Comparing value
     * 
     */
    public function where($field, $operator = null, $value = null) {
        if (strlen($field) && is_null($operator) && is_null($value)) {
            // For RAW string
            array_push($this->_where, $field);
        } else {
            if (is_null($value)) {
                $value = $operator;
                $operator = '=';
            }

            if (is_array($value)) {
                $str = '(';
                
                for ($i = 0; $i < count($value); $i++) {
                    $d = $value[$i];
                    $str .= is_numeric($d) ? $d : "'$d'";
                    $str .= count($value) === $i + 1 ? '' : ', ';
                }
                
                $str .= ')';
                
                $value = $str;
                
                array_push($this->_where, $field . ' ' . $operator . $value);
            } else {
                $value = (string)$value;
                array_push($this->_where, $field . ' ' . $operator . (!strcasecmp($operator, 'in') ? " ($value)" : ' \'' . $value . '\''));
            }
        }
        
        return $this;
    }
    
    /**
     * Or statements
     * 
     * @param   string  $field Table field
     * @param   mixed   $operator   Comparer
     * @param   mixed   $value  Comparing value
     * 
     */
    public function orWhere($field, $operator = null, $value = null) {
        if (strlen($field) && is_null($operator) && is_null($value)) {
            // For RAW string
            array_push($this->_or, $field);
        } else {
            if (is_null($value)) {
                $value = $operator;
                $operator = '=';
            }
            
            if (is_array($value)) {
                $str = '(';
                
                for ($i = 0; $i < count($value); $i++) {
                    $d = $value[$i];
                    $str .= is_numeric($d) ? $d : "'$d'";
                    $str .= count($value) === $i + 1 ? '' : ', ';
                }
                
                $str .= ')';
                
                $value = $str;
                
                array_push($this->_or, $field . ' ' . $operator . $value);
            } else {
                $value = (string)$value;
                array_push($this->_or, $field . ' ' . $operator . (!strcasecmp($operator, 'in') ? " ($value)" : ' \'' . $value . '\''));
            }
        }
        
        return $this;
    }
    
    /**
     * Select distinct only
     * 
     */
    public function distinct() {
        $this->_distinct = 'DISTINCT ';
        
        return $this;
    }
    
    /**
     * Order by ascending
     * 
     * @param   string/array    $fields
     * 
     */
    public function asc($fields) {
        if (is_array($fields)) {
            $this->_order = ' ORDER BY ' . implode($fields, ', ') . ' ASC';
        } else {
            $this->_order = ' ORDER BY ' . $fields . ' ASC';
        }
         
        return $this;
    }
    
    /**
     * Order by descending
     * 
     * @param   string/array    $fields
     * 
     */
    public function desc($fields) {
        if (is_array($fields)) {
            $this->_order = ' ORDER BY ' . implode($fields, ', ') . ' DESC';
        } else {
            $this->_order = ' ORDER BY ' . $fields . ' DESC';
        }
         
        return $this;
    }
    
    /**
     * Set limit
     * 
     * @param   int     $start
     * @param   int     $offset
     * 
     */
    public function limit($start, $offset = null) {
        $this->_limit = ' LIMIT ' . $start;
        
        if (!is_null($offset)) {
            $this->_limit .= ', ' . $offset;    
        }
        
        return $this;
    }
    
    /**
     * Join Tables
     * 
     * @param   string  $table  Table name
     * @param   string  $field1  Table.Fieldname
     * @param   string  $operator Operator
     * @param   string  $field2  Table.Fieldname
     * 
     * @example join('users', 'users.id', '=', 'login.id')
     */
    public function join($table, $field1, $operator, $field2) {
        array_push($this->_joins, ' INNER JOIN ' . $table . ' ON ' . $field1 . ' ' . $operator . ' ' . $field2 . ' ');
    
        return $this;
    }
    
    /**
     * Left Join Tables
     * 
     * @param   string  $table  Table name
     * @param   string  $field1  Table.Fieldname
     * @param   string  $operator Operator
     * @param   string  $field2  Table.Fieldname
     *
     * @example leftJoin('users', 'users.id', '=', 'login.id')
     * 
     */
    public function leftJoin($table, $field1, $operator, $field2) {
        array_push($this->_joins, ' LEFT JOIN ' . $table . ' ON ' . $field1 . ' ' . $operator . ' ' . $field2 . ' ');
    
        return $this;
    }
    
    /**
     * Right Join Tables
     * 
     * @param   string  $table  Table name
     * @param   string  $field1  Table.Fieldname
     * @param   string  $operator Operator
     * @param   string  $field2  Table.Fieldname
     *
     * @example rightJoin('users', 'users.id', '=', 'login.id')
     * 
     */
    public function rightJoin($table, $field1, $operator, $field2) {
        array_push($this->_joins, ' RIGHT JOIN ' . $table . ' ON ' . $field1 . ' ' . $operator . ' ' . $field2 . ' ');
    
        return $this;
    }
    
    /**
     * Group fields by
     * 
     * @param   string  $field
     * 
     */
    public function groupBy($field) {
        $this->_group_by = ' GROUP BY ' . $field . ' ';
        
        return $this;
    }
    
    /**
     * Get last executed query
     * 
     * @return string
     * 
     */
    public static function getLastQuery() {
        return count(self::$_last_commands) ? self::$_last_commands[count(self::$_last_commands) - 1] : '';
    }
    
    /**
     * Get query history
     * 
     * @return array
     * 
     */
    public static function getQueryHistory() {
       return count(self::$_last_commands) ? self::$_last_commands : null; 
    }
    
    /**
     * Raw sql command
     * 
     * @param string $sql SQL commands
     * 
     */
    public static function raw($sql) {
        $new_self = new self();
        $new_self->_sql_query = $sql;
        
        return $new_self;
    }
    
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
                Log::error($this->_mysqli->connect_error);
            }
        } else {
            Log::error('Lollipop is initialized with wrong database configuration');
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
                    return Cache::recover($cache_key);
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

?>
