<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\App;
use \Lollipop\Config;

/**
 * Text Class 
 *
 * @version     2.0
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for string
 * 
 */
class Text
{
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
        $text_sec_method = Config::get('text') && Config::get('text')->security && Config::get('text')->security->method
                            ? Config::get('text')->security->method : 'AES256';
        $text_sec_key = is_null($key) && Config::get('text') && Config::get('text')->security && Config::get('text')->security->key
                            ? md5(Config::get('text')->security->key) : md5($key);
        $text_sec_iv =  Config::get('text') && Config::get('text')->security && Config::get('text')->security->iv
                            ? Config::get('text')->security->iv : substr(md5(App::SUGAR), 0, 16);

        return openssl_encrypt($string, $text_sec_method, $text_sec_key, false, $text_sec_iv);
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
        $text_sec_method = Config::get('text') && Config::get('text')->security && Config::get('text')->security->method
                            ? Config::get('text')->security->method : 'AES256';
        $text_sec_key = is_null($key) && Config::get('text') && Config::get('text')->security && Config::get('text')->security->key
                            ? md5(Config::get('text')->security->key) : md5($key);
        $text_sec_iv =  Config::get('text') && Config::get('text')->security && Config::get('text')->security->iv
                            ? Config::get('text')->security->iv : substr(md5(App::SUGAR), 0, 16);
        
        return openssl_decrypt($cipher, $text_sec_method, $text_sec_key, false, $text_sec_iv);
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
        $string = stripslashes($string);
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
     * Splits string
     * 
     * @param   string  $string     String to be split
     * @param   string  $token      Token
     * 
     * @return  array
     */
    static function split($string, $token) {
        $array = array();
        
        $tok = strtok($string, $token);
        
        while ($tok != false) {
            array_push($array, $tok);
            
            $tok = strtok($token);
        }
        
        return $array;
    }
}

?>
