<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;

/**
 * Log Class
 * 
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Log's messages to file and thows exception
 */
class Log
{
    /**
     * Messages
     * 
     * @type    array   Messages
     * 
     */
    private static $_messages = [
            'info' => [],
            'warn' => [],
            'error' => [],
            'fatal' => [],
            'debug' => []
        ];
    
    /**
     * Check if log file is busy
     * This is will make sure of file locking with file_put_contents
     * 
     * @type    boolean
     * 
     */
    private static $_busy ;
    
    /**
     * Append to log file
     * 
     * @param   string  $message    Message log
     * 
     * @throws  \Lollipop\Exception\Runtime
     * @throws  \Lollipop\Exception\Argument
     * 
     * @return  bool
     * 
     */
    private static function _write($type, $message) {
        $log_path = Config::get('log.folder', LOLLIPOP_STORAGE_LOG);
        $log_enable = Config::get('log.enable', true);
        $log_hourly = Config::get('log.hourly', false);
        
        if (!is_dir($log_path))
           throw new \Lollipop\Exception\Runtime('Log folder doesn\'t exists'); 
        
        if (!is_writeable($log_path))
           throw new \Lollipop\Exception\Runtime('Log folder is not writeable'); 

        if (!isset(self::$_messages[$type]))
            throw new \Lollipop\Exception\Argument('Invalid log type');
        
        // Save to memory
        array_push(self::$_messages[$type], $message);
        
        if ($log_enable && !self::$_busy) {
            self::$_busy = true;
            
            // Create filename base on configuration (daily or hourly)
            $filename = $log_path . DIRECTORY_SEPARATOR . ($log_hourly ? date('Y-m-d-H') : date('Y-m-d')) . '.log';
            
            // Save to file
            file_put_contents($filename, date('Y-m-d H:i:s') . ' [' . strtoupper($type) . '] ' . $message . "\n", FILE_APPEND);
            
            self::$_busy = false;
        }
    }
    
    /**
     * Log information message
     * 
     * @param   string  $message    Message
     * 
     * @return  void
     * 
     */
    public static function info($message) {
        self::_write('info', $message);
    }
    
    /**
     * Log warning message
     * 
     * @param   string  $message    Message
     * 
     * @return  void
     * 
     */
    public static function warn($message) {
        self::_write('warn', $message);
    }
    
    /**
     * Log error message
     * 
     * @param   string  $message    Message
     * 
     * @return  void
     * 
     */
    public static function error($message) {
        self::_write('error', $message);
    }
    
    /**
     * Log fatal message
     * 
     * @param   string  $message    Message
     * 
     * @return  void
     * 
     */
    public static function fatal($message) {
        self::_write('fatal', $message);
    }
    
    /**
     * Log debug message
     * 
     * @param   string  $message    Message
     * 
     * @return  void
     * 
     */
    public static function debug($message) {
        self::_write('debug', $message);
    }
    
    /**
     * Get messages
     * 
     * @param   string  $type   Message type
     * @return  array
     * 
     */
    public static function get($type = null) {
        return is_null($type) ? self::$_messages : (isset(self::$_messages[$type]) ? self::$_messages[$type] : []);
    }
}
