<?php

namespace Lollipop;

use \Lollipop\HTTP\Router;

/**
 * Lollipop Application
 * 
 * @package Lollipop
 * @author  John Aldrich Bernardo <4ldrich@protonmail.com>
 * 
 */
class App
{
    private static $path = '';
    private static $registered_dirs = [];
    private static $running = false;

    private static function on($path) {
        if (self::$running) {
            throw new \Lollipop\Exception\Runtime('Application is already running.');
        }

        self::$path = rtrim($path, '/');

        spl_autoload_register(function($class) {
            // project-specific namespace prefix
            $prefixes = array(
                'App\\Controller\\'    => self::$path . '/controller/',
                'App\\Model\\'         => self::$path . '/model/',
                'App\\Helper\\'        => self::$path . '/helper/',
                'App\\Middleware\\'    => self::$path . '/middleware/'
            );

            foreach ($prefixes as $prefix => $base_dir) {
                // does the class use the namespace prefix?
                $len = strlen($prefix);
                
                if (strncmp($prefix, $class, $len) !== 0) {
                    // no, move to the next registered prefix
                    continue;
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
            }
        });
    }

    public static function path($path) {
        return self::$path ? self::$path . "/$path" : $path;
    }

    public static function isRunning() {
        return self::$running;
    }

    public static function run($path, $render = true) {
        if (self::$running) {
            throw new \Lollipop\Exception\Runtime('Application is already running.');
        }

        self::on($path);
        $response = \Lollipop\HTTP\Router::dispatch($render);
        self::$running = true;

        return $response;
    }
}
