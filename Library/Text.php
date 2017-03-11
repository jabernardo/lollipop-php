<?php

namespace Lollipop;

/**
 * Text Class 
 *
 * @version     1.1
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
        if (is_null($key)) {
            $encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(\Lollipop\App::SUGAR), $string, MCRYPT_MODE_CBC, md5(md5(\Lollipop\App::SUGAR))));
        } else {
            $encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
        }
        
        return $encoded;
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
        if (is_null($key)) {
            $decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(\Lollipop\App::SUGAR), base64_decode($cipher), MCRYPT_MODE_CBC, md5(md5(\Lollipop\App::SUGAR))), '\0');
        } else {
            $decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cipher), MCRYPT_MODE_CBC, md5(md5($key))), '\0');
        }
        
        return trim($decoded);
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
