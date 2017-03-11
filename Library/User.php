<?php

namespace Lollipop;

/**
 * User Class
 *
 * @version     2.0
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for User login
 * 
 */
class User {

    /**
     * @const Privilege Type: USER
     * 
     */
    CONST ROLE_USER = 'USER';

    /**
     * @const Privilege Type: ADMIN
     *
     */
    CONST ROLE_ADMIN = 'ADMIN';

    /**
     * @var     object  $_db    MySQLi instance
     *
     */
    private static $_db = null;
    
    /**
     * @var     int     $_ulimit User limit
     *
     */
    private static $_ulimit = 0;
    
    /**
     * Connect to database
     * 
     * @param   string  $host   Database host
     * @param   string  $uid    User id/username/email
     * @param   string  $pwd    Password
     * @param   int     $users_limit    Limit of online users
     *
     */
    static private function connect() {
        $db = \Lollipop\Config::get('db');

        if (is_object($db)) {
            $host = isset($db->host) ?  $db->host : 'localhost';
            $uid = isset($db->username) ?  $db->username : 'root';
            $pwd = isset($db->password) ?  $db->password : '';
            $db = isset($db->database) ?  $db->database : 'lollipop';
            
            // Instantiate MySQLi
            self::$_db = new \mysqli($host, $uid, $pwd, $db);
            
            if (self::$_db->connect_errno > 0) {
                \Lollipop\Log::error(self::$_db->connect_error);
            }
            
            /**
             * Check if 'login' table exists
             * 
             *   CREATE TABLE IF NOT EXISTS `login` (
             *     `id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
             *     `username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
             *     `role` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
             *     `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
             *     `user_agent` text COLLATE utf8_unicode_ci NOT NULL,
             *     `last_in` datetime NOT NULL,
             *     PRIMARY KEY (`id`)
             *   ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
             *
             */
            self::$_db->query(base64_decode('Q1JFQVRFIFRBQkxFIElGIE5PVCBFWElTVFMgYGxvZ2luYCAoDQogIGBpZGAgdmFyY2hhcigzMikgQ09MTEFURSB1dGY4X3VuaWNvZGVfY2kgTk9UIE5VTEwsDQogIGB1c2VybmFtZWAgdmFyY2hhcig0MCkgQ09MTEFURSB1dGY4X3VuaWNvZGVfY2kgTk9UIE5VTEwsDQogIGByb2xlYCB2YXJjaGFyKDQwKSBDT0xMQVRFIHV0ZjhfdW5pY29kZV9jaSBOT1QgTlVMTCwNCiAgYGlwX2FkZHJlc3NgIHZhcmNoYXIoNDUpIENPTExBVEUgdXRmOF91bmljb2RlX2NpIE5PVCBOVUxMLA0KICBgdXNlcl9hZ2VudGAgdGV4dCBDT0xMQVRFIHV0ZjhfdW5pY29kZV9jaSBOT1QgTlVMTCwNCiAgYGxhc3RfaW5gIGRhdGV0aW1lIE5PVCBOVUxMLA0KICBQUklNQVJZIEtFWSAoYGlkYCkNCikgRU5HSU5FPUlubm9EQiBERUZBVUxUIENIQVJTRVQ9dXRmOCBDT0xMQVRFPXV0ZjhfdW5pY29kZV9jaTs='));
            
            // Get users limit
            self::$_ulimit = \Lollipop\Config::get('users_limit') ? \Lollipop\Config::get('users_limit') : 1000000000;
            
            // Auto logout users
            self::_autoLogout();
            
            // Update last in
            self::_setAsActive();
            
            // Check if user's IP is still the same
            self::_checkIP();
        } else {
            \Lollipop\Log::error('Lollipop is not initialized with wrong configuration');
        }
    }
    
    /**
     * Close MySQL connection
     * 
     * @return  void
     * 
     */
    static private function disconnect() {
        if (!self::$_db) {
            self::$_db->close();
        }
    }
    
    /**
     * Log in user or check if user is logged
     *
     * @param   string  $uid    User id/username/email
     * @param   string  $role   User type
     * @param   string  $anchor     Page to go after login
     *
     * @return  int     -1 Not connected to database, 1 if users are too many, and 0 if logged in.
     *
     */
    static function in($uid = '', $role = 'USER', $anchor = null) {
        self::connect();
        
        if (!is_null(self::$_db)) {
            // Check if user is logged in
            if (!strlen($uid)) {
                $sidsession = !is_null(\Lollipop\Cookie::get('lsuid')) ? \Lollipop\Cookie::get('lsuid') : \Lollipop\Session::get('lsuid');
                
                $query = self::$_db->query('SELECT COUNT(`id`) FROM login WHERE `id` = \'' . $sidsession . '\'');
                
                if ($query) {
                    $results = $query->fetch_array();
                    $islogged = (int)$results[0];
                }

                return (bool)$islogged;
            }
            
            // Check if we have reached the maximum users available for login
            $query = self::$_db->query('SELECT COUNT(`id`) FROM login');
            
            if ($query) {
                $logged = $query->fetch_array();
                $logged = (int)$logged[0];
            }

            if ($logged < self::$_ulimit + 1) {
                // Login user
                $session_id = md5(\Lollipop\Cookie::key() . $uid);
                
                if (self::in()) {
                    \Lollipop\Log::error('An user was already logged-in.');
                }
                
                self::$_db->query('INSERT INTO login(`id`, `username`, `role`, `ip_address`, `user_agent`, `last_in`) VALUES(\'' . $session_id . '\', \'' . $uid . '\', \'' . $role . '\', \'' . self::_getUserIP() . '\', \'' .  $_SERVER['HTTP_USER_AGENT'] . '\', NOW())');
                self::disconnect();
                
                // Save user's session id in cookie and session
                \Lollipop\Cookie::set('lsuid', $session_id);
                \Lollipop\Session::set('lsuid', $session_id);

                // If anchor is available then redirect page
                if (!is_null($anchor)) {
                    $anchor = ($anchor == '/') ? \Lollipop\Url::here() : $anchor;
                    \Lollipop\Page::redirect($anchor);
                }
            } else {
                self::disconnect();

                return 1; // Number users logged in exceeded
                //throw new \Exception('User logged in exceeded');
            }
        } else {
            //return -1; // Not connected to the database
            \Lollipop\Log::error('Not connected to the database. Please initialize Lollipop');
        }
    }
    
    /**
     * Log out a user
     *
     * @return  void
     *
     */
    static function out() {
        self::connect();

        if (is_null(self::$_db)) {
            return -1; // Not connected to the database
            //throw new \Exception('Not connected to the database');
        }
        
        $sidsession = !is_null(\Lollipop\Cookie::get('lsuid')) ? \Lollipop\Cookie::get('lsuid') : \Lollipop\Session::get('lsuid');
        
        self::$_db->query('DELETE FROM login WHERE `id` = \'' . $sidsession . '\'');
        $sidsession = null;

        \Lollipop\Cookie::drop('lsuid');
        \Lollipop\Session::drop('lsuid');

        self::disconnect();
    }
    
    /**
     * Get username of active user
     *
     * @return  string
     *
     */
    static function getUsername() {
        self::connect();

        if (is_null(self::$_db)) {
            return -1; // Not connected to the database
            //throw new \Exception('Not connected to the database');
        }
        
        $sidsession = !is_null(\Lollipop\Cookie::get('lsuid')) ? \Lollipop\Cookie::get('lsuid') : \Lollipop\Session::get('lsuid');

        $query = self::$_db->query('SELECT `username` FROM login WHERE `id` = \'' . $sidsession . '\'');
        
        if ($query) {
            $user = $query->fetch_array();
        
            self::disconnect();

            return $user[0];
        }
        
        self::disconnect();

        return null;
    }

    /**
     * Check if user logged-in is admin
     *
     * @return  bool
     *
     */
    static function isAdmin() {
        self::connect();

        if (is_null(self::$_db)) {
            return -1; // Not connected to the database
            //throw new \Exception('Not connected to the database');
        }
        
        $sidsession = !is_null(\Lollipop\Cookie::get('lsuid')) ? \Lollipop\Cookie::get('lsuid') : \Lollipop\Session::get('lsuid');
        
        $query = self::$_db->query('SELECT `role` FROM login WHERE `id` = \'' . $sidsession . '\' AND `role` = \'' . self::ROLE_ADMIN . '\'');
        
        if ($query) {
            if (count($query->fetch_array())) {
                return true;
            }
        }
        
        self::disconnect();

        return false;
    }

    /** 
     * Check if user has the specific role
     *
     * @param   string  $role  User role
     *
     * @return  bool
     *
     */
    static function hasRole($role) {
        self::connect();

        if (is_null(self::$_db)) {
            return -1; // Not connected to the database
            //throw new \Exception('Not connected to the database');
        }
        
        $sidsession = !is_null(\Lollipop\Cookie::get('lsuid')) ? \Lollipop\Cookie::get('lsuid') : \Lollipop\Session::get('lsuid');
        
        $query = self::$_db->query('SELECT `role` FROM login WHERE `id` = \'' . $sidsession . '\' AND `role` = \'' . $role . '\'');
        
        if ($query) {
            if (count($query->fetch_array())) {
                return true;
            }
        }
        
        self::disconnect();

        return false;
    }
    
    /**
     * Update `last_in` of current user
     * 
     * @return  void
     * 
     */
    static private function _setAsActive() {
        $sidsession = !is_null(\Lollipop\Cookie::get('lsuid')) ? \Lollipop\Cookie::get('lsuid') : \Lollipop\Session::get('lsuid');
        
        $query = self::$_db->query('SELECT COUNT(`id`) FROM login WHERE `id` = \'' . $sidsession . '\'');
        
        if ($query) {
            $islogged = $query->fetch_array();
            $islogged = (int)$islogged[0];
        }
        
        if ($islogged) {
            self::$_db->query('UPDATE login SET `last_in` = NOW() WHERE `id` = \'' . $sidsession . '\'');
        }
    }
    
    /**
     * This automatically logout users that are last active about an hour
     * 
     * @return  bool
     */
    static private function _autoLogout() {
        if (is_null(self::$_db)) {
            return -1; // Not connected to the database
            //throw new \Exception('Not connected to the database');
        }
        
        $query = self::$_db->query('DELETE FROM `login` WHERE last_in < NOW() - INTERVAL 1 HOUR');
        
        return $query ? true : false;
    }
    
    /**
     * Check if session is still the same
     * 
     * @return  void
     * 
     */
    static private function _checkIP() {
        $sidsession = !is_null(\Lollipop\Cookie::get('lsuid')) ? \Lollipop\Cookie::get('lsuid') : \Lollipop\Session::get('lsuid');

        if ($sidsession) {
            $query = self::$_db->query('SELECT `ip_address` FROM login WHERE `id` = \'' . $sidsession . '\'');
            
            if ($query) {
                $results = $query->fetch_array();
                
                $ip_address = isset($results['ip_address']) ? $results['ip_address'] : null;
                
                if (!is_null($ip_address)) {
                    // If IP for current user is altered with different one or VPN
                    // Kick him out
                    if ($ip_address != self::_getUserIP()) {
                        self::$_db->query('DELETE FROM login WHERE `id` = \'' . $sidsession . '\'');
            
                        \Lollipop\Cookie::drop('lsuid');
                        \Lollipop\Session::drop('lsuid');
                    }
                }
            }
        }
    }
    
    /**
     * Get clients IP
     * 
     * @return  string
     * 
     */
    static private function _getUserIP() {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        
        return null;
    }
}

?>
