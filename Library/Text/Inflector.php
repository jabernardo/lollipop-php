<?php

namespace Lollipop\Text;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

/**
 * Inflector Class
 *
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

        return lcfirst($str);
    }

    /**
     * Changes word format to studly
     *
     * @param   string  $str    String
     *
     * @return  string
     */
    static function studly($str) {
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
        return strtolower(str_replace([ ' ', '\\', '/', ':', '*', '?', '<', '>', '|' ], '_', $str));
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
        return str_replace(' ', '-', urlencode($str));
    }

    /**
     * Censor words
     * 
     * @param   string  $str    Input string
     * @param   array   $censored   Bad words
     * @param   string  $replacement    Replacement string ('')
     * @return  string
     * 
     */
    static function censor($str, array $censored, $replacement = '') {
        foreach ($censored as $bad) {
            $str = preg_replace("/$bad/i", $replacement, $str);
        }

        return $str;
    }
}
