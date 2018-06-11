<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;

/**
 * Text Class 
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for string
 * 
 */
class Text
{
    /**
     * Get encryption method
     * 
     * @access  private
     * @return  string
     * 
     */
    private static function _getSecMethod() {
        return Config::get('security.text.method', 'AES256');
    }
    
    /**
     * Get encryption key
     * 
     * @access  private
     * @return  string
     * 
     */
    private static function _geteSecKey() {
        return md5(Config::get('security.text.key', SUGAR));
    }
    
    /**
     * Get encryption iv
     * 
     * @access  private
     * @return  string
     * 
     */
    private static function _getSecIv() {
        return Config::get('security.text.iv', substr(md5(SUGAR), 0, 16));
    }
    
    /**
     * Checks if a string contains another string
     * 
     * @paramstring     $haystack   String where to find the occurance
     * @param   string  $needle     String to be found
     * 
     * @return  bool
     */
    static function contains($haystack, $needle) {
        return (bool)strpos($haystack, $needle);
    }
    
    /**
     * Encrypts values passed
     *
     * @param   string  $string     String to be encoded.
     * @param   string  $key        Passphrase
     * 
     * @return  string
     */
    static function lock($string, $key = null) {
        return openssl_encrypt($string, self::_getSecMethod(), spare($key, self::_geteSecKey()), false, self::_getSecIv());
    }

    /**
     * Decrypts values passed
     *
     * @param   string  $cipher     Code to be decrypt.
     * @param   string  $key        Passphrase
     * 
     * @return  string
     */
    static function unlock($cipher, $key = null) {
        return openssl_decrypt($cipher, self::_getSecMethod(), spare($key, self::_geteSecKey()), false, self::_getSecIv());
    }

    /**
     * Alias addslashes
     *
     * @param   string  $string     String
     * 
     * @return  string
     */
    static function escape($string) {
        return addslashes($string);
    }

    /**
     * Returns HTML displayable string
     *
     * @param   string  $string     String
     * 
     * @return  string
     */
    static function entities($string) {
        $string = htmlentities($string);
        $string = nl2br($string);
        
        return $string;
    }

    /**
     * Random string
     *
     * @param   string  $length     Random string length
     * 
     * @return  string
     */
    static function random($length) {
        if (!is_numeric($length)) return null;
        
        $chars = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(1, 4));
        
        return substr(str_shuffle($chars), 0, $length);
    }

    /**
     * Repeat text
     * 
     * @param   string  $str    String to be repeated
     * @param   int     $n      Number of repetition
     * 
     */
    static function repeat($str, $n) {
        $nstr = '';

        for ($i = 0; $i < $n; $i++) {
            $nstr .= $str;
        }

        return $nstr;
    }

    /**
     * Splits string
     * 
     * @param   string  $string     String to be split
     * @param   string  $token      Token
     * 
     * @return  array
     */
    static function split($string, $token) {
        $array = [];
        
        $tok = strtok($string, $token);
        
        while ($tok != false) {
            array_push($array, $tok);
            
            $tok = strtok($token);
        }
        
        return $array;
    }
}
