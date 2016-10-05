<?php
    namespace Lollipop;

    /**
     * Filter Class
     *
     * @version     1.0
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
         *
         * @return  bool
         */
        static function name($string) {
            return (!preg_match('/[0-9!@#$%\^&\*\(\)\+_\\\|=\[\]\{\}\<\>\/?\,;:\'\"~`]/', $string) && strlen($string) > 1);
        }

        /**
         * Checks string if is a valid contact number_format
         *
         * @param   string  $string     String
         *
         * @return  bool
         */
        static function contact($string) {
            return (preg_match('/\+[0-9]{12}|[0-9]{11}|[0-9]{3}-[0-9]{3}-[0-9]{4}/', $string));
        }

        /**
         * Checks string is is a valid email address
         *
         * @param   string  $string     String
         *
         * @return  bool
         */
        static function email($string) {
            return filter_var($string, FILTER_VALIDATE_EMAIL) ? true : false;
        }
    }
?>
