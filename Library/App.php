<?php

namespace Lollipop;

use \Lollipop\Benchmark;
use \Lollipop\Config;
use \Lollipop\Log;

/**
 * Lollipop Application Class
 *
 * @version     6.1
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop
 * @uses        \Lollipop\Config
 *              \Lollipop\Benchmark
 *              \Lollipop\Log
 *
 */
class App
{
    /**
     * @var     string  Default encryption key
     *
     */
    CONST SUGAR = 'MTAwMDA1ODA3MTMyMjEy';

    /**
     * @type    array   Autoload folders
     *
     */
    static private $_autoload_folders = array();

    /**
     * Initialize Lollipop
     *
     * @param   mixed   $config     Configuration variable
     *
     * @type    void
     *
     */
    static public function init(array $config = array()) {
        // Parse config
        //  configuration can be on multidimensional array or
        if (is_array($config)) {
            foreach ($config as $key => $value) {
                Config::add($key, $value);
            }
        }

        // Set application environment
        self::_setEnvironment();

        // Check for folders available for autoloading
        if (!is_null(Config::get('autoload'))) {
            foreach ((array)Config::get('autoload') as $autoload_folder) {
                array_push(self::$_autoload_folders, $autoload_folder);
            }
        }

        // Register autoload
        spl_autoload_register(function($class) {
            foreach (self::$_autoload_folders as $aufolder) {
                $f = $aufolder . '/' . $class . '.php';

                if (file_exists($f)) {
                    require_once($f);
                }
            }
        });
    }

    /**
     * Get response application response time
     *
     * @return  mixed
     *
     */
    static public function getResponseTime() {
        Benchmark::mark('_l_app_end');

        return Benchmark::elapsedTime('_l_app_start', '_l_app_end');
    }

    /**
     * Get Benchmark
     *
     * @return  mixed
     *
     */
    static public function getBenchmark() {
        Benchmark::mark('_l_app_end');

        return Benchmark::elapsed('_l_app_start', '_l_app_end');
    }

    /**
     * Set environment for application
     *
     * @example
     *          'environment'   =>  'dev' or 'development'
     *          'environment'   =>  'stg' or 'staging'
     *          'environment'   =>  'prd' or 'production'
     *
     * @return  void
     *
     */
    static private function _setEnvironment() {
        switch(strtolower(Config::get('environment') ? Config::get('environment') : 'dev')) {
            case 'dev':
            case 'development':
                // Report all errors
                error_reporting(E_ALL);

                break;
            case 'stg':
            case 'staging':
                // Report all errors except E_NOTICE
                error_reporting(E_ALL & ~E_NOTICE);

                break;
            case 'prd':
            case 'production':
                // Turn off error reporting
                error_reporting(0);

                break;
            default:
                Log::error('Invalid application environment: ' . Config::get('environment'));

                break;
        }
    }
}

?>
