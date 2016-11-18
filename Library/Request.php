<?php
    namespace Lollipop;
    
    /**
     * Request Class 
     *
     * @version     1.1
     * @author      John Aldrich Bernardo
     * @email       4ldrich@protonmail.com
     * @package     Lollipop 
     * @description Class containing usable functions for get and post request
     * 
     */
    class Request
    {
        /**
         * Check for request(s)
         *
         * @param   array   $requests   Request names
         *
         * @return bool
         * 
         */
        static function is($requests) {
            $is = true;
            
            // Also support PUT and DELETE
            parse_str(file_get_contents("php://input"), $_php_request);
            // Merge with POST and GET
            $_all_requests = array_merge($_REQUEST, $_php_request);
            
            if (is_array($requests)) {
                $returns = array();
                
                foreach ($requests as $request) {
                    array_push($returns, isset($_all_requests[$request]));
                }
                
                foreach ($returns as $return) {
                    if ($return == false) {
                        $is = false;
                    }
                }
            } else {
                $is = isset($_all_requests[$requests]);
            }
            
            return $is;
        }
        
        /**
         * Gets values of request(s)
         *
         * @param   array   $requests   Request names
         *
         * @return  array
         * 
         */
        static function get($requests) {
            $var = array();
            
            // Also support PUT and DELETE
            parse_str(file_get_contents("php://input"), $_php_request);
            // Merge with POST and GET
            $_all_requests = array_merge($_REQUEST, $_php_request);
            
            if (is_array($requests)) {
                foreach ($requests as $request) {
                    $var[$request] = isset($_all_requests[$request]) ? $_all_requests[$request] : null;
                }
            } else {
                $var = (isset($_all_requests[$requests])) ? $_all_requests[$requests] : null;
            }
            
            return $var;
        }
    }
?>
