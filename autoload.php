<?php

/**
 * Lollipop Autoload 
 * 
 * Set application constant variables for directory structure
 * and register autoload for loading libraries.
 * 
 * @version 4.1.2
 * @author  John Aldrich Bernardo
 * @email   4ldrich@protonmail.com
 * 
 */

/**
 * Application Sugar
 * 
 */
define('SUGAR', 'MTAwMDA1ODA3MTMyMjEy');
 
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
    // project-specific namespace prefix
    $prefix = 'Lollipop\\';

    // base directory for the namespace prefix
    $base_dir = LOLLIPOP_LIBRARY;

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});


/**
 * Execute bootstrap file for Application
 * 
 */
require_once(LOLLIPOP_BASE . 'bootstrap.php');

?>
