<?php
    namespace Lollipop;
    
    /**
     * Log Class
     * 
     * @version     1.0
     * @author      John Aldrich Bernardo
     * @email       4ldrich@protonmail.com
     * @package     Lollipop 
     * @description Log's messages to file and thows exception
     */
    class Log
    {
        /**
         * Append to log file
         * 
         * @param   string  $message    Message log
         * 
         * @return  bool
         * 
         */
        private static function __writeOutLog($message) {
            $log_path = (\Lollipop\Config::get('log_folder') ? \Lollipop\Config::get('log_folder'): LOLLIPOP_STORAGE_LOG);
            
            if (!is_dir($log_path)) {
               throw new \Exception('Log folder doesn\'t exists.'); 
            }
            
            if (!is_writeable($log_path)) {
               throw new \Exception('Log folder is not writeable.'); 
            }
            
            
            $filename = $log_path . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
            
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
            self::__writeOutLog('WARNING: ' . date('H:i:s') . ': ' . $message);
        }
        
        /**
         * Log error message
         * 
         * @param   string  $message    Message
         * 
         * @return  void
         * 
         */
        public static function error($message, $exception = true) {
            self::__writeOutLog('ERROR: ' . date('H:i:s') . ': ' . $message);
            
            if ($exception) {
                throw new \Exception($message);
            }
        }
        
        /**
         * Log notification message
         * 
         * @param   string  $message    Message
         * 
         * @return  void
         * 
         */
        public static function notify($message) {
            self::__writeOutLog('NOTIFICATION: ' . date('H:i:s') . ': ' . $message);
        }
    }

?>
