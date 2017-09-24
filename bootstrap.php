<?php

/**
 * Lollipop-PHP Bootstrap File
 * 
 * @version 6.2.0
 * @author  John Aldrich Bernardo
 * @email   4ldrich@protonmail.com
 * 
 */

/**
 * Check if PHP version is >= 5.4
 * 
 */
if (!function_exists('phpversion')) {
    exit('You PHP is too old! Try upgrading.' . PHP_EOL);
}

$_lol_toks = explode('.', phpversion());

if (count($_lol_toks) >= 2) {
    $_lol_major_minor = (double)($_lol_toks[0] . '.' . $_lol_toks[1]);
    
    if ($_lol_major_minor < (5.4)) {
        exit('You PHP is too old! Try upgrading.' . PHP_EOL);
    }
} else {
    exit('The version of your PHP can\'t be verified' . PHP_EOL);
}

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
 * Set spare value for empty values
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
 * Get value if variable is set and not null
 *
 * @param    reference   &$var  Variable
 * @return   mixed
 *
 */
function getvar(&$var) {
    return isset($var) && $var ? $var : false;
}

/**
 * Lollipop error handler
 * 
 * @param   int     $errno      Error number
 * @param   string  $errstr     Message
 * @param   string  $errfile    Filename
 * @param   int     $errline    Line
 * @return  void
 * 
 */
function lollipop_error_handler($errno, $errstr, $errfile, $errline) {
    switch ($errno) {
        case E_USER_WARNING:
            \Lollipop\Log::warn($errstr . ' on \'' . $errfile . ':' . $errline . '\'');
            break;
        case E_USER_NOTICE:
            \Lollipop\Log::notice($errstr . ' on \'' . $errfile . ':' . $errline . '\'');
            break;
        default:
            \Lollipop\Log::error($errstr . ' on \'' . $errfile . ':' . $errline . '\'');
            break;
    }
}

/**
 * Exception handler
 * 
 * @param   stdClass    Exception class instance
 * @return  void
 * 
 */
function lollipop_exception_handler($ex) {
    \Lollipop\Log::error($ex->getMessage());
}

/**
 * Register error handlers
 * 
 */
set_exception_handler('lollipop_exception_handler');
set_error_handler('lollipop_error_handler');
