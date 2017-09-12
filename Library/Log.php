<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;

/**
 * Log Class
 * 
 * @version     2.1.1
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
    private static $_messages = array(
                    'info' => array(),
                    'warn' => array(),
                    'error' => array(),
                    'notice' => array()
                );
    
    /**
     * Append to log file
     * 
     * @param   string  $message    Message log
     * 
     * @return  bool
     * 
     */
    private static function __writeOutLog($message) {
        $config = Config::get('log');

        $log_path = spare($config->folder, LOLLIPOP_STORAGE_LOG);
        $log_enable = spare($config->enable, true);
        $log_hourly = spare($config->hourly, false);
        
        if (!is_dir($log_path)) {
           die('Lollipop Application has been terminated due to unhandled error: Log folder doesn\'t exists.'); 
        }
        
        if (!is_writeable($log_path)) {
           die('Lollipop Application has been terminated due to unhandled error: Log folder is not writeable.'); 
        }
        
        $filename = $log_path . DIRECTORY_SEPARATOR . ($log_hourly ? date('Y-m-d-H') : date('Y-m-d')) . '.log';
        
        if ($log_enable)
            file_put_contents($filename, $message . "\n", FILE_APPEND);
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
        array_push(self::$_messages['info'], $message);
        self::__writeOutLog('INFO: ' . date('H:i:s') . ': ' . $message);
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
        array_push(self::$_messages['warn'], $message);
        self::__writeOutLog('WARNING: ' . date('H:i:s') . ': ' . $message);
    }
    
    /**
     * Log error message
     * 
     * @param   string  $message    Message
     * @param   bool    $die        Die
     * 
     * @return  void
     * 
     */
    public static function error($message, $die = false) {
        array_push(self::$_messages['error'], $message);
        self::__writeOutLog('ERROR: ' . date('H:i:s') . ': ' . $message);
        
        if ($die) exit('Lollipop Application has been terminated due to unhandled error: ' . $message);
    }
    
    /**
     * Log notification message
     * 
     * @param   string  $message    Message
     * 
     * @return  void
     * 
     */
    public static function notice($message) {
        array_push(self::$_messages['notice'], $message);
        self::__writeOutLog('NOTICE: ' . date('H:i:s') . ': ' . $message);
    }
    
    /**
     * Get messages
     * 
     * @param   string  $type   Message type
     * @return  array
     * 
     */
    public static function get($type = null) {
        return is_null($type) ? self::$_messages : (isset(self::$_messages[$type]) ? self::$_messages[$type] : array());
    }
}

?>
