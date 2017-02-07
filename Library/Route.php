<?php
    namespace Lollipop;

    /**
     * Lollipop Route Class
     *
     * @version     1.4.2
     * @author      John Aldrich Bernardo
     * @email       4ldrich@protonmail.com
     * @package     Lollipop
     *
     */
    class Route
    {
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
         * GET route
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
            self::serve('GET', $path, $callback, $cachable, $cachable);
        }

        /**
         * POST route
         *
         * @param   string      $path       Route
         * @param   function    $callback   Callback function
         * @param   bool        $cachable   Is page cache enable? (default is false)
         * @param   int         $cache_time Cache time (in minutes 1440 or 24 hrs default)
         *
         * @return  void
         *
         */
        static public function post($path, $callback, $cachable = false, $cache_time = 24) {
            self::serve('POST', $path, $callback, $cachable, $cachable);
        }

        /**
         * PUT route
         *
         * @param   string      $path       Route
         * @param   function    $callback   Callback function
         * @param   bool        $cachable   Is page cache enable? (default is false)
         * @param   int         $cache_time Cache time (in minutes 1440 or 24 hrs default)
         *
         * @return  void
         *
         */
        static public function put($path, $callback, $cachable = false, $cache_time = 24) {
            self::serve('PUT', $path, $callback, $cachable, $cachable);
        }

        /**
         * DELETE route
         *
         * @param   string      $path       Route
         * @param   function    $callback   Callback function
         * @param   bool        $cachable   Is page cache enable? (default is false)
         * @param   int         $cache_time Cache time (in minutes 1440 or 24 hrs default)
         *
         * @return  void
         *
         */
        static public function delete($path, $callback, $cachable = false, $cache_time = 24) {
            self::serve('DELETE', $path, $callback, $cachable, $cachable);
        }

        /**
         * ALL route
         *
         * @param   string      $path       Route
         * @param   function    $callback   Callback function
         * @param   bool        $cachable   Is page cache enable? (default is false)
         * @param   int         $cache_time Cache time (in minutes 1440 or 24 hrs default)
         *
         * @return  void
         *
         */
        static public function all($path, $callback, $cachable = false, $cache_time = 24) {
            self::serve('', $path, $callback, $cachable, $cachable);
        }

        /**
         * Serve route
         *
         * @param   string      $method     Request method
         * @param   string      $path       Route
         * @param   function    $callback   Callback function
         * @param   bool        $cachable   Is page cache enable? (default is false)
         * @param   int         $cache_time Cache time (in minutes 1440 or 24 hrs default)
         *
         * @return  void
         *
         */
        static public function serve($method, $path, $callback, $cachable = false, $cache_time = 24) {
            // Store route
            self::$_stored_routes[$path] = array(
                                                'request_method' => is_array($method) ? array_map('strtoupper', $method) : strtoupper($method),
                                                'callback' => $callback,
                                                'cachable' => $cachable,
                                                'cache_time' => $cache_time
                                            );

            self::_registerDispatch();
        }

        /**
         * Set header
         *
         * @param   mixed    $headers    HTTP header
         * @return  void
         *
         */
        static public function setHeader($headers) {
            // Record HTTP header
            if (is_array($headers)) {
                foreach ($headers as $header) {
                    header($header);
                }
            } else if (is_string($headers)) {
                header($headers);
            }
        }

        /**
         * Route forwarding
         *
         * @param string $path Route
         * @param array  $params Arguments
         *
         */
        static public function forward($path, array $params = array()) {
            if (isset(self::$_stored_routes[$path])) {
                $callback = self::$_stored_routes[$path];
                $callback = $callback['callback'];

                $data = self::_callback($callback, $params);

                echo self::_returnData($data);
            } else {
                self::$_is_listening = false;
                self::_checkNotFound();
            }
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
                    $path = str_replace(array('(%s)', '(%d)', '(%%)', '/'), array('(\w+)', '(\d+)', '(.*)', '\/'), trim($path, '/'));
                    // Request URL
                    $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
                    // Active script or running script (this is when no redirection is being done in .htaccess)
                    $as =  str_replace('/', '\/', trim($_SERVER["SCRIPT_NAME"], '/') . ($path ? '/' : ''));

                    $is_match = preg_match('/^' . $path . '$/i', $url, $matches) ||
                                preg_match('/^' . $as . $path . '$/i', $url, $matches);

                    // Check if request method matches                    
                    if (isset($route['request_method'])) {
                        $_rm = $route['request_method'];
                        
                        if ((is_array($_rm) && !in_array($_SERVER['REQUEST_METHOD'], $_rm)) ||
                            (is_string($_rm) && $_rm !== $_SERVER['REQUEST_METHOD'] && $_rm !== '')) {
                            $is_match = false;
                        }
                    }

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
                            if (\Lollipop\Config::get('dev_tools')) {
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
                            $data = self::_callback($callback, $matches);
                            
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
         * Call callback and return data
         *
         * @access  private
         * @param   mixed   $callback   (string or callable) string must be {abc}.{abc} format to use controller action
         * @oaram   array   $args       Parameters to be passed to callback
         * @return  mixed
         *
         */
        static private function _callback($callback, array $args = array()) {
            if (is_string($callback)) {
                // If callback was string then
                // Explode it by (dot) to determine the Controller and Action
                $ctoks = explode('.', $callback);
                
                switch (count($ctoks)) {
                    case 1: // Function only
                        if (!function_exists($action = $ctoks[0])) {
                            \Lollipop\Log::error('Callback is not a function', true);
                        }
                        
                        return call_user_func_array($action, $args); // Update callback
                        
                        break;
                    case 2: // Controller and Action
                        if (class_exists($ctoks[0]) &&
                            is_callable(array($controller = new $ctoks[0], $action = $ctoks[1]))) {
                            
                            
                            return call_user_func_array(array($controller, $action), $args);
                        }
                        
                        \Lollipop\Log::error('Can\'t find controller and action', true);
                    
                        break;
                    
                    default: // Invalid callback
                        \Lollipop\Log::error('Callback is not a function', true);
                        
                        break;
                }
            }
            
            // Only if sent parameter is callable
            if (is_callable($callback)) {
                return call_user_func_array($callback, $args); // Return anonymous function
            }
            
            // Automatically return null from everything that is not an string or callable
            return 'Invalid callback.';
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
            if (is_array($data) || is_object($data)) {
                self::setHeader('Content-type: application/json');

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
            if (!self::$_is_listening && (\Lollipop\Config::get('show_not_found') === null || \Lollipop\Config::get('show_not_found') !== false)) {
                \Lollipop\Log::notify('404 Not Found: ' . $_SERVER['REQUEST_URI']);

                header('HTTP/1.0 404 Not Found');

                if (!is_null(\Lollipop\Config::get('not_found_page'))) {
                    require_once(\Lollipop\Config::get('not_found_page'));
                } else {
                    echo '<!DOCTYPE html>';
                    echo '<!-- Lollipop for PHP by John Aldrich Bernardo -->';
                    echo '<html>';
                    echo '<head><title>404 Not Found</title></head>';
                    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
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
