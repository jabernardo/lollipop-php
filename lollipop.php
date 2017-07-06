<?php

/**
 * Lollipop
 * An extensive and flexible library for PHP
 *
 * @package    Lollipop
 * @version    6.1.3
 * @author     John Aldrich Bernardo <bjohnaldrich@gmail.com>
 * @copyright  Copyright (C) 2015-2017 John Aldrich Bernardo. All rights reserved.
 * @license
 *
 * Copyright (c) 2015-2017 John Aldrich Bernardo
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR 
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */

/**
 * Check if PHP version is valid
 * 
 */
if (!function_exists('phpversion')) {
    exit('You PHP is too old! Try upgrading.');
}

$_lol_toks = explode('.', phpversion());

if (count($_lol_toks) >= 2) {
    $_lol_major_minor = (double)($_lol_toks[0] . '.' . $_lol_toks[1]);
    
    /**
     * if PHP version is 5.3 or below exit
     * 
     */
    if ($_lol_major_minor < (5.4)) {
        exit('You PHP is too old! Try upgrading.');
    }
} else {
    exit('The version of your PHP can\'t be verified');
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

