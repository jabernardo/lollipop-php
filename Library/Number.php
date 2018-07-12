<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

/**
 * Number Class
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for number
 * 
 */
class Number
{
    /**
     * Constant for currency
     * 
     */
    CONST CURRENCY_EURO = 128;
    CONST CURRENCY_GBP  = 163;
    CONST CURRENCY_USD  = 36;
    CONST CURRENCY_PESO = 80;
    CONST CURRENCY_YEN  = 165;
    
    /**
     * Checks if number is in between the given range
     *
     * @param   double  $num    Number
     * @param   double  $from   Start of range
     * @param   double  $to     Range
     * 
     * @return  bool
     */
    static function between($num, $from, $to) {
        return (((double)$num >= (double)$from) && ((double)$num <= (double)$to));
    }

    /**
     * Returns currency value
     *
     * @param   double  $number     Number
     * @param   int     $decimal    Decimal places
     * @param   int     $currency   Currency symbol
     * 
     * @return  string
     */
    static function currency($number, $decimal = 2, $currency = self::CURRENCY_PESO) {
        if (!self::parsable($number)) return null;

        return chr($currency) . ' ' . number_format(round($number, $decimal), $decimal);
    }

    /**
     * Gets next fibonacci number
     *
     * @param   int     Number
     * 
     * @return  int
     */
    static function fibonacci($number) {
        if ((int)$number == 0 || (int)$number == 1) return 1;

        return self::fibonacci((int)$number - 1) + self::fibonacci((int)$number - 2);
    }

    /**
     * Gets factorial of a number
     *
     * @param   int     $number     Number
     * 
     * @return  int
     */
    static function factorial($number) {
        $fac = 1;

        for ($i = 1; $i <= (int)$number; $i++) $fac *= (int)$i;

        return $fac;
    }

    /**
     * Returns if string is parsable as number
     *
     * @param   string  $string     String
     * 
     * @return  bool
     */
    static function parsable($string) {
        return is_numeric($string);
    }

    /**
     * Returns percentage
     *
     * @param   double  $number Number
     * @param   int     $decimal    Decimal places
     * 
     * @return  string
     */
    static function percentage($number, $decimal = 2) {
        if (!self::parsable($number)) return null;

        return round($number, $decimal) . '%';
    }
    
    /**
     * Returns human readble size equivalent of bytes
     *
     * @param   double  $size   Size in bytes
     * 
     * @return  string
     */
    static function readableSize($size) {
        if ((double)$size < 1024) {
            return round((double)$size, 0) . ' Bytes';
        } else if ((double)$size < 1048576) {
            return round((double)$size / 1024, 2) . ' KB';
        } else if ((double)$size < 1073741824) {
            return round((double)$size / 1048576, 2) . ' MB';
        } else {
            return round((double)$size / 1073741824, 2) . ' GB';
        }

        return $size . ' Bytes';
    }
}
