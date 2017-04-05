<?php

/**
 * Lollipop Autoload 
 * 
 * @version 4.1.0
 * @author  John Aldrich Bernardo
 * 
 */
 
/**
 * Document root
 * 
 */
define('DOCUMENT_ROOT', str_replace(DIRECTORY_SEPARATOR, '/', $_SERVER['DOCUMENT_ROOT']));

/**
 * Lollipop directory
 * 
 * 
 */
define('LOLLIPOP_BASE', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__) . '/'));

/**
 * Lollipop classes directory
 * 
 * 
 */
define('LOLLIPOP_LIBRARY', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_BASE . 'Library/'));

/**
 * Lollipop Storage directory
 * 
 * 
 */
define('LOLLIPOP_STORAGE', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_BASE . 'Storage/'));

/**
 * Lollipop cache directory
 * 
 * 
 */
define('LOLLIPOP_STORAGE_CACHE', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_STORAGE . 'cache/'));

/**
 * Lollipop cache directory
 * 
 * 
 */
define('LOLLIPOP_STORAGE_LOG', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_STORAGE . 'logs/'));

/**
 * __autoload function
 *
 */
function autoLoader($class) {
    $tokens = explode('\\', $class);

    if (count($tokens) == 2) {
        $file = LOLLIPOP_LIBRARY . ucfirst($tokens[1]) . '.php';

        if (file_exists($file)) {
            require_once($file);
        }
    }
}

// Register autoloader
spl_autoload_register('autoLoader');

// Include namespace
require_once(LOLLIPOP_BASE . 'lollipop.php');

?>
