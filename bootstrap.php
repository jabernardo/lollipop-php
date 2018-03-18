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
 * Set spare value for null values
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
    \Lollipop\Log::error('Exception received with message "' . $ex->getMessage() . '" on ' . $ex->getFile() . ':' . $ex->getLine());
}

/**
 * Register error handlers
 * 
 */
set_exception_handler('lollipop_exception_handler');
set_error_handler('lollipop_error_handler');
