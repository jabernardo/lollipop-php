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
            
            if (is_array($requests)) {
                $returns = array();
                
                foreach ($requests as $request) {
                    array_push($returns, isset($_REQUEST[$request]));
                }
                
                foreach ($returns as $return) {
                    if ($return == false) {
                        $is = false;
                    }
                }
            } else {
                $is = isset($_REQUEST[$requests]);
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
            
            if (is_array($requests)) {
                foreach ($requests as $request) {
                    $var[$request] = isset($_REQUEST[$request]) ? $_REQUEST[$request] : null;
                }
            } else {
                $var = (isset($_REQUEST[$requests])) ? $_REQUEST[$requests] : null;
            }
            
            return $var;
        }
    }
?>
