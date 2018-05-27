<?php

/**
 * Check if PHP version is >= 5.4
 * 
 */
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    exit('You PHP is too old! Try upgrading.' . PHP_EOL);
}

/**
 * Start session
 * 
 */
if (!isset($_SESSION)) session_start();

/**
 * Alternate a value to undefined variable
 * 
 * @param   reference   &$opt1  Variable
 * @param   mixed       $opt2   Alternative value
 * @return  mixed
 * 
 */
function fuse(&$opt1, $opt2) {
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
function spare($value, $spare) {
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
function spare_nan($value, $spare) {
    return !is_null($value) ? $value : $spare;
}

/**
 * Get value if variable is set and not null
 *
 * @param    reference   &$var  Variable
 * @return   mixed
 *
 */
function getvar(&$var) {
    return isset($var) && $var ? $var : false;
}

