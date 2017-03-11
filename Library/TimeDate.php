<?php

namespace Lollipop;

/**
 * Date Class
 *
 * @version     1.0
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for Date and Time
 * 
 */
class TimeDate
{
    /**
     * Checks if string is valid time or date
     *
     * @param   string  $string     Raw string
     *
     * @return  bool
     */
    static function parsable($string) {
        try {
            $tmp = new \DateTime($string);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Converts string to DateTime
     *
     * @param   string      $string Raw string
     *
     * @return  DateTime
     */
    static function fromString($string) {
        if (self::parsable($string)) return new \DateTime($string);

        return null;
    }

    /**
     * Returns current date and time
     *
     * @param   string  $format	Format of date and time to return
     *
     * @return  string
     */
    static function now($format = '') {
        if (strlen($format) > 0) {
            return date($format);
        } else {
            return date('m.d.y h:i:s');
        }
    }

    /**
     * Returns date in long date format
     *
     * @param   string  $date Date
     * @return  string   Long Date
     *
     * @return  string
     */
    static function longDateString($date) {
        str_replace('now', date('m.d.y'), $date);

        if (!self::parsable($date)) return null;

        $r = self::fromString($date);

        return $r->format('M d, Y');
    }


    /**
     * Returns date in short date format
     *
     * @param   string  $date       Date
     * @param   string  $separator  Separator
     *
     * @return  string
     */
    static function shortDateString($date, $separator = '/') {
        str_replace('now', date('m.d.y'), $date);

        if (!self::parsable($date)) return null;

        $r = self::fromString($date);

        return $r->format('m' . $separator . 'd' . $separator . 'Y');
    }

    /**
     * Returns time in long time format
     *
     * @param   string  $time   Time
     *
     * @return  string
     */
    static function longTimeString($time) {
        str_replace('now', date('h:i:s t'), $time);

        if (!self::parsable($time)) return null;

        $r = self::fromString($time);

        return $r->format('h:i:s a');
    }

    /**
     * Returns time in short time format
     *
     * @param   string  $time   Time
     *
     * @return  string
     */
    static function shortTimeString($time) {
        str_replace('now', date('h:i:s t'), $time);

        if (!self::parsable($time)) return null;

        $r = self::fromString($time);

        return $r->format('H:i:s');
    }
}

?>
