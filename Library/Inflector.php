<?php

namespace Lollipop;

/**
 * Inflector Class
 *
 * @version     1.1
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing functions to Change the form of a word in
 *              accordance as required by the grammatical rules of the language
 * 
 */
class Inflector
{
    /**
     * Changes word format to camelize
     *
     * @param   string  $str    String
     *
     * @return  string
     */
    static function camelize($str) {
        // john_aldrich -> john aldrich
        $str = str_replace('_', ' ', $str);

        // john aldrich -> John Aldrich
        $str = ucwords($str);

        // John Aldrich -> JohnAldrich
        $str = str_replace(' ', '', $str);

        return $str;
    }

    /**
     * Convert string into filename case
     *
     * @param   string  $str     Original filename
     *
     * @return 	string
     */
    static function filename($str) {
        return strtolower(str_replace(array(' ', '\\', '/', ':', '*', '?', '<', '>', '|'), '_', $str));
    }

    /**
     * Convert string into human readable format
     *
     * @param   string  $str    String
     *
     * @return  string
     */
    static function humanize($str) {
        return str_replace('_', ' ', $str);
    }

    /**
     * Convert spaces into underscores
     *
     * @param   string  $str    String
     *
     * @return  string
     */
    static function underscore($str) {
        return str_replace(' ', '_', $str);
    }

    /**
     * Convert into string a safe url string
     *
     * @param   string  $str    String
     *
     * @return  string
     */
    static function url($str) {
        return str_replace(' ', '-', $str);
    }
}

?>
