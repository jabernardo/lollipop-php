<?php
    namespace Lollipop;

    /**
     * Filter Class
     *
     * @version     1.2
     * @author      John Aldrich Bernardo
     * @email       4ldrich@protonmail.com
     * @package     Lollipop 
     * @description Class containing usable functions to filter string
     * 
     */
    class Filter
    {
        /**
         * Checks string if is a valid name
         *
         * @param   string  $string	String
         * @return  mixed   Returns <false> if string is not valid for name usage
         * 
         */
        static function name($string) {
            return (!preg_match('/[0-9!@#$%\^&\*\(\)\+_\\\|=\[\]\{\}\<\>\/?\,;:\'\"~`]/', $string) && strlen($string) > 1) ? $string : false;
        }

        /**
         * Checks string if is a valid contact number_format
         *
         * @param   string  $string     String
         * @return  mixed   Returns <false> if string is not valid for mobile or telephone number
         * 
         */
        static function contact($string) {
            return (preg_match('/\+[0-9]{12}|[0-9]{11}|[0-9]{3}-[0-9]{3}-[0-9]{4}/', $string)) ? $string : false;
        }

        /**
         * Checks string is is a valid email address
         *
         * @param   string  $string     String
         * @return  bool    Returns <false> if string is not valid for email usage
         * 
         */
        static function email($string) {
            return filter_var($string, FILTER_VALIDATE_EMAIL) ? $string : false;
        }
        
        /**
         * Check if string is a valid URL
         * 
         * @param   string  $string     String
         * @return  bool    Returns <false> if string is not valid for url
         * 
         */
        static function url($string) {
            return filter_var($string, FILTER_VALIDATE_URL) ? $string : false;
        }
        
        /**
         * Check if string is a valid IP
         * 
         * @param   string  $string     String
         * @return  bool    Returns <false> if string is not valid for ip
         * 
         */
        static function ip($string) {
            return filter_var($string, FILTER_VALIDATE_IP) ? $string : false;
        }
    }
?>
