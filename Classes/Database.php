<?php
    namespace Lollipop;
    
    /**
     * Database Driver for MySQLi
     *
     * @package     Candy
     * @version     2.0
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
         * Updates
         * 
         * @type array
         * 
         */
        private $_updates = array();
        
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
        private $_where = array();
        
        /**
         * Or statements
         * 
         * @type array
         * 
         */
        private $_or = array();
        
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
        private $_joins = array();
        
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
        private static $_last_commands = array();
        
        /**
         * Select table
         * 
         * @param   string  $table
         * 
         */
        public static function table($table) {
            $new_self = new self();
            $new_self->_table = $table;

            return $new_self;
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
            
            // Execute query
            $return =  $this->__execute();

            // Return contents
            $results = array();
            
            if (is_object($return)) {
                while ($row = $return->fetch_array()) {
                    array_push($results, $row);
                }
            }
            
            return $results ? $results : null;
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
            
            // Execute query
            $return =  $this->__execute();

            // Return contents
            $results = array();
            
            if (is_object($return)) {
                while ($row = $return->fetch_array()) {
                    array_push($results, $row);
                }
            }
            
            return $results ? $results : null;
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
            $_tmp_fields = array();
            $_tmp_values = array();
            
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
            $q = $this->__execute();
            
            return ($q) ? $this : false;
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
            
            // Execute query
            $q = $this->__execute();
            
            return ($q) ? $this : false;
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
            
            // Execute query
            $q = $this->__execute();
            
            return ($q) ? $this : false;
        }
        
        /**
         * Sum rows in field/s
         * 
         * @param   array   Field/s name
         * 
         */
        public function sum($field) {
            // Temporary key sum strings
            $_tmp_sum = array();
            
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
            
            // Execute query
            $return = $this->__execute();

            $results = array();
            
            if (is_object($return)) {
                $results = $return->fetch_array();
            }
            
            return count($results) ? $results : null;
        }
        
        /**
         * Count rows in field/s
         * 
         * @param   array   Field/s name
         * 
         */
        public function count($field) {
            // Temporary key sum strings
            $_tmp_sum = array();
            
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
            
            // Execute query
            $return = $this->__execute();

            $results = array();
            
            if (is_object($return)) {
                $results = $return->fetch_array();
            }
            
            return count($results) ? $results : null;
        }
        
        /**
         * Where statements
         * 
         * @param   string  $field Table field
         * @param   string  $operator   Comparer
         * @param   string  $value  Comparing value
         * 
         */
        public function where($field, $operator = '', $value = '') {
            if (strlen($field) && strlen($operator) == 0 && strlen($value) == 0) {
                // For RAW string
                array_push($this->_where, $field);
            } else {
                if (strlen($value) == 0) {
                    $value = $operator;
                    $operator = '=';
                }
                
                if (is_string($value)) {
                    array_push($this->_where, $field . ' ' . $operator . ' \'' . $value . '\'');
                } else {
                    array_push($this->_where, $field . ' ' . $operator . ' ' . $value);
                }
            }
            
            return $this;
        }
        
        /**
         * Or statements
         * 
         * @param   string  $field Table field
         * @param   string  $operator   Comparer
         * @param   string  $value  Comparing value
         * 
         */
        public function orWhere($field, $operator = '', $value = '') {
            if (strlen($field) && strlen($operator) == 0 && strlen($value) == 0) {
                // For RAW string
                array_push($this->_or, $field);
            } else {
                if (strlen($value) == 0) {
                    $value = $operator;
                    $operator = '=';
                }
                
                if (is_string($value)) {
                    array_push($this->_or, $field . ' ' . $operator . ' \'' . $value . '\'');
                } else {
                    array_push($this->_or, $field . ' ' . $operator . ' ' . $value);
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
         * Set raw sql query
         * 
         */
        public function executeRaw($sql) {
            $this->_sql_query = $sql;
            
            // Execute
            $result = $this->__execute();
            
            return $result;
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
         * Run raw sql command
         * 
         * @param string $sql SQL commands
         * 
         */
        public static function raw($sql) {
            $_exec_raw = new self();
            return $_exec_raw->executeRaw($sql);
        }
        
        /**
         * Connect to MySQL server
         *
         * @return  void
         *
         */
        private function __connect() {
            $db = \Lollipop\App::getConfig('db');

            if (!is_null($db)) {
                $host = !is_null($db['host']) ?  $db['host'] : 'localhost';
                $uid = !is_null($db['username']) ?  $db['username'] : 'root';
                $pwd = !is_null($db['password']) ?  $db['password'] : '';
                $db = !is_null($db['database']) ?  $db['database'] : 'lollipop';
                       
                // Instantiate MySQLi
                $this->_mysqli = new \mysqli($host, $uid, $pwd, $db);
                
                if ($this->_mysqli->connect_errno > 0) {
                    \Lollipop\Log::error($this->_mysqli->connect_error);
                }
            } else {
                \Lollipop\Log::error('Lollipop is not initialized with wrong database configuration');
            }
        }

        /**
         * Execute query
         * 
         */
        private function __execute() {
            // @todo Execute sql here
            if (strlen($this->_sql_query)) {
                // Open connection
                $this->__connect();

                // Execute command
                $return = $this->_mysqli->query($this->_sql_query);

                // Log executed query
                array_push(self::$_last_commands, $this->_sql_query);

                // Close connection
                $this->_mysqli->close();

                return $return;
            }
        }
    }
    
?>
