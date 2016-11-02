<?php
    /**
     * Lollipop Autoload 
     * 
     * @version 4.0.0
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
    define('LOLLIPOP_CLASS', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_BASE . 'Classes/'));
    
    /**
     * Lollipop cache directory
     * 
     * 
     */
    define('LOLLIPOP_CACHE', str_replace(DIRECTORY_SEPARATOR, '/',  LOLLIPOP_BASE . 'Cache/'));
    
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
     * __autoload function
     *
     */
    function autoLoader($class) {
        $tokens = explode('\\', $class);

        if (count($tokens) == 2) {
            $file = LOLLIPOP_CLASS . ucfirst($tokens[1]) . '.php';

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
