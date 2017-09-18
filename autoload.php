<?php

/**
 * Lollipop Autoload 
 * 
 * Set application constant variables for directory structure
 * and register autoload for loading libraries.
 * 
 * @version 4.1.1
 * @author  John Aldrich Bernardo
 * @email   4ldrich@protonmail.com
 * 
 */
 
/**
 * Document root
 * 
 * Path: /var/www/{application}
 * 
 */
define('DOCUMENT_ROOT', str_replace(DIRECTORY_SEPARATOR, '/', $_SERVER['DOCUMENT_ROOT']));

/**
 * Lollipop directory
 * 
 * Path: {vendor/lollipoop-php}
 * 
 */
define('LOLLIPOP_BASE', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__) . '/'));

/**
 * Lollipop classes directory
 * 
 * Path: {vendor/lollipop-php/Library}
 * 
 */
define('LOLLIPOP_LIBRARY', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_BASE . 'Library/'));

/**
 * Lollipop Storage directory
 * 
 * Path: {vendor/lollipop-php/Storage}
 * 
 */
define('LOLLIPOP_STORAGE', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_BASE . 'Storage/'));

/**
 * Lollipop cache directory
 * 
 * Path: {vendor/lollipop-php/Storage/cache}
 * 
 */
define('LOLLIPOP_STORAGE_CACHE', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_STORAGE . 'cache/'));

/**
 * Lollipop local database directory
 * 
 * Path: {vendor/lollipop-php/Storage/db}
 * 
 */
define('LOLLIPOP_STORAGE_LOCALDB', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_STORAGE . 'db/'));

/**
 * Lollipop cache directory
 * 
 * Path: {vendor/lollipop-php/Storage/logs}
 * 
 */
define('LOLLIPOP_STORAGE_LOG', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_STORAGE . 'logs/'));


/**
 * Register Autoload for Lollipop Libraries
 * 
 * \Lollipop\{Name}
 * 
 */
spl_autoload_register(function ($class) {
    $tokens = explode('\\', $class);

    if (count($tokens) == 2) {
        $file = LOLLIPOP_LIBRARY . ucfirst($tokens[1]) . '.php';

        if (file_exists($file)) {
            require_once($file);
        }
    }
});


/**
 * Execute bootstrap file for Application
 * 
 */
require_once(LOLLIPOP_BASE . 'bootstrap.php');

?>
