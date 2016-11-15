<?php
    namespace Lollipop;

    /**
     * Lollipop Application Class
     *
     * @version     6.0
     * @author      John Aldrich Bernardo
     * @email       4ldrich@protonmail.com
     * @package     Lollipop
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
         * @type    array   Configuration settings
         *
         */
        static private $_config = array();

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
        static public function init($config = array()) {
            // Parse config
            //  configuration can be on multidimensional array or
            if (is_array($config)) {
                foreach ($config as $key => $value) {
                    self::$_config[$key] = $value;
                }
            // Initialization files (*.ini)
            } else if (file_exists($config)) {
                self::$_config = parse_ini_file($config);
            }

            // Set application environment
            self::_setEnvironment();

            // Check for folders available for autoloading
            if (!is_null(self::getConfig('autoload'))) {
                if (is_array(self::getConfig('autoload'))) {
                    foreach (self::getConfig('autoload') as $autoload_folder) {
                        array_push(self::$_autoload_folders, $autoload_folder);
                    }
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
            \Lollipop\Benchmark::mark('_l_app_end');

            return \Lollipop\Benchmark::elapsedTime('_l_app_start', '_l_app_end');
        }

        /**
         * Get Benchmark
         *
         * @return  mixed
         *
         */
        static public function getBenchmark() {
            \Lollipop\Benchmark::mark('_l_app_end');

            return \Lollipop\Benchmark::elapsed('_l_app_start', '_l_app_end');
        }

        /**
         * Get configuration key value
         *
         * @return  string
         *
         */
        static public function getConfig($key) {
            return isset(self::$_config[$key]) ? self::$_config[$key] : null;
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
            switch(strtolower(self::getConfig('environment') ? self::getConfig('environment') : 'dev')) {
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
                    Log::error('Invalid application environment: ' . self::getConfig('environment'));

                    break;
            }
        }
    }
?>
