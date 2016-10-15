<?php
    namespace Lollipop;
    
    /**
     * Lollipop Application Class
     * 
     * @version     4.2
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
         * @type    bool    Do we find a route?
         * 
         */
        static private $_is_listening = false;
        
        
        /**
         * @type    bool    Is Dispatch function already registered on shutdown?
         * 
         */
        static private $_dispatch_registered = false;
        
        /**
         * @type    array   Stored callbacks
         * 
         */
        static private $_stored_routes = array();
        
        /**
         * @type    bool    Is dispatcher running
         * 
         */
        static private $_is_running = false;
        
        /**
         * @type    array   HTTP page headers set by user
         * 
         */
        static private $_page_headers = array();

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
            
            // Register dispatch function
            self::_registerDispatch();
        }

        /**
         * Get route
         *
         * @param   string      $path       Route
         * @param   function    $callback   Callback function
         * @param   bool        $cachable   Is page cache enable? (default is false)
         * @param   int         $cache_time Cache time (in minutes 1440 or 24 hrs default)
         * 
         * @return  void
         *
         */
        static public function get($path, $callback, $cachable = false, $cache_time = 24) {
            // Store route
            self::$_stored_routes[$path] = array(
                                                'callback' => $callback,
                                                'cachable' => $cachable,
                                                'cache_time' => $cache_time
                                            );
            
            self::_registerDispatch();
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
         * Set header
         * 
         * @param string    $key    HTTP header key
         * @param string    $value  HTTP header value
         * 
         */
        static public function setHeader($key, $value) {
            $header = $key . ': ' . $value;
            
            // Record HTTP header
            array_push(self::$_page_headers, $header);
            
            // Set header
            header($header);
        }
        
        /**
         * Route forwarding
         * 
         * @param string $path Route
         * @param array  $params Arguments
         * 
         */
        static public function forward($path, $params = null) {
            if (isset(self::$_stored_routes[$path])) {
                $callback = self::$_stored_routes[$path];
                $callback = $callback['callback'];
                
                $data = $callback($params);
                
                echo self::_returnData($data);
            } else {
                self::$_is_listening = false;
                self::_checkNotFound();
            }
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
         * Dispath function
         * 
         * 
         * @return void
         * 
         */
        static private function _dispatch() {
            if (is_array(self::$_stored_routes)) {
                // Lollipop Application Start
                \Lollipop\Benchmark::mark('_l_app_start');
                
                // Check if page is not found
                register_shutdown_function(function() {
                    self::_checkNotFound();
                });

                foreach (self::$_stored_routes as $path => $route) {
                    // Callback for route
                    $callback = isset($route['callback']) ? $route['callback'] : function(){};
                    // Check if route or url is cachable (defaults to false)
                    $cachable = isset($route['cachable']) ? $route['cachable'] : false;
                    // Cache time
                    $cache_time = isset($route['cache_time']) ? $route['cache_time'] : 1;
                    // Translate regular expressions
                    $path = str_replace(array('(:alpha)', '(:num)', '(:all)', '/'), array('([a-zA-Z \-_]+)', '([0-9]+)', '(.*)', '\/'), trim($path, '/'));
                    // Request URL
                    $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
                    // Active script or running script (this is when no redirection is being done in .htaccess)
                    $as =  str_replace('/', '\/', ltrim($_SERVER["SCRIPT_NAME"], '/') . '/');
                    
                    $is_match = preg_match('/^' . $path . '$/i', $url, $matches) ||
                                preg_match('/^' . $as . $path . '$/i', $url, $matches);
                    
                    if ($is_match) {
                        if (!self::$_is_running && !self::$_is_listening) {
                            // Mark that the router already found a match
                            self::$_is_listening = true;
                            
                            // Remove unneeded data
                            array_shift($matches);
                            
                            // Cache key 
                            $cache_key = $path;
                            
                            if ($matches) {
                                $cache_key .= '|' . implode(',', $matches);
                            }
                            
                            // Mark dispatcher is currently running
                            self::$_is_running = true;
                            
                            // Enable dev cache options
                            if (self::getConfig('dev_tools')) {
                                // ?purge_all_cache
                                if (isset($_REQUEST['purge_all_cache'])) {
                                    \Lollipop\Cache::purge();
                                }
                            }
                            
                            // If page is not cacheable make sure to remove existing keys
                            if (!$cachable) {
                                \Lollipop\Cache::remove($cache_key);
                            }
                            
                            // Check if page cache is enabled and recover cache if available
                            // Also we could use ?nocache as parameter
                            // to force caching to be disabled
                            if ($cachable && !isset($_REQUEST['nocache']) && \Lollipop\Cache::exists($cache_key)) {
                                $page_cache = \Lollipop\Cache::recover($cache_key);
                                
                                // Recover HTTP headers from cache
                                if (isset($page_cache['HTTP_HEADER'])) {
                                    foreach($page_cache['HTTP_HEADER'] as $header) {
                                        header($header);
                                    }
                                }
                                
                                // Output from cache
                                echo isset($page_cache['HTTP_CONTENT']) ? $page_cache['HTTP_CONTENT'] : '';
                                
                                exit; // Just recover this page
                            }
                            
                            // Start ob
                            ob_start();
                            
                            // If not from Controller, then just call function
                            $data = call_user_func_array($callback, $matches);
                            
                            // Show output
                            echo self::_returnData($data);
    
                            // Mark as dispatched
                            self::$_dispatch_registered = true;
                            
                            // Save cache
                            if ($cachable && !isset($_REQUEST['nocache'])) {
                                $page_cache = array(
                                        'HTTP_HEADER' => headers_list(),
                                        'HTTP_CONTENT' => ob_get_contents(),
                                        'DATE_CREATE' => date('Y-m-d H:i:s')
                                    );
                                
                                \Lollipop\Cache::save($cache_key, $page_cache, false, $cache_time);
                            }
                            
                            // Flush ob contents
                            ob_flush();
                            
                            // Off
                            self::$_is_running = false;
                        } else {
                            Log::error('Dispatcher is already running. Can\'t run multiple routes.');
                        }
                        
                        exit; // Just stop it
                    }
                }
            }
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
        
        /**
         * Register dispatch function on shutdown
         * 
         * @return  void
         * 
         */
        static private function _registerDispatch() {
            // Register dispatch function
            if (!self::$_dispatch_registered) {
                register_shutdown_function(function() {
                    // Dispatch the page
                    self::_dispatch();
                });
            }
        }
        
        /**
         * Return string value for data
         * 
         * @param   object  $data   Data to convert
         * 
         * @return  string
         * 
         */
        static private function _returnData($data) {
            if (!$data) {
                // Throw 404 not found if $data is empty
                self::_checkNotFound();
            }
            
            // If data is in array format then set content-type
            // to application/json
            if (!count(self::$_page_headers) && (is_array($data) || is_object($data))) {
                self::setHeader('Content-type', 'application/json');
                
                return json_encode($data);
            }
            
            return $data;
        }
        
        /**
         * Check if any of routes doesn't match
         *
         * @return  void
         *
         */
        static private function _checkNotFound() {
            if (!self::$_is_listening && (self::getConfig('show_not_found') === null || self::getConfig('show_not_found') !== false)) {
                \Lollipop\Log::notify('404 Not Found: ' . $_SERVER['REQUEST_URI']);
                
                header('HTTP/1.0 404 Not Found');

                if (!is_null(self::getConfig('not_found_page'))) {
                    readfile(self::getConfig('not_found_page'));
                } else {
                    echo '<!DOCTYPE html>';
                    echo '<!-- Lollipop for PHP by John Aldrich Bernardo -->';
                    echo '<html>';
                    echo '<head><title>404 Not Found</title></head>';
                    echo '<body>';
                    echo '<h1>404 Not Found</h1>';
                    echo '<p>The page that you have requested could not be found.</p>';
                    echo '</body>';
                    echo '</html>';
                }

                exit;
            }
        }
    }
?>
