<?php

namespace Lollipop\Utils;

/**
 * Variable Utilities
 * 
 * @package Lollipop
 * @author  John Aldrich Bernardo <4ldrich@protonmail.com>
 * 
 * 
 */
trait Vars
{
    /**
     * Alternate a value to undefined variable
     * 
     * @param   reference   &$opt1  Variable
     * @param   mixed       $opt2   Alternative value
     * @return  mixed
     * 
     */
    static function fuse(&$opt1, $opt2) {
        return isset($opt1) ? $opt1 : (isset($opt2) ? $opt2 : null); 
    }
    
    /**
     * Set spare value for empty variables
     * 
     * @param   mixed   $value  Primary value
     * @param   mixed   $spare  Spare value
     * @return  mixed
     * 
     */
    static function spare($value, $spare) {
        return $value ? $value : $spare;
    }
    
    /**
     * Set spare value for null variables
     * 
     * @param   mixed   $value  Primary value
     * @param   mixed   $spare  Spare value
     * @return  mixed
     * 
     */
    static function spareNan($value, $spare) {
        return !is_null($value) ? $value : $spare;
    }
    
    /**
     * Get value if variable is set and not null
     *
     * @param    reference   &$var  Variable
     * @return   mixed
     *
     */
    static function getVar(&$var) {
        return isset($var) && $var ? $var : false;
    }
}
