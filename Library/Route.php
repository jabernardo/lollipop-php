<?php

namespace Lollipop;

/**
 * Lollipop Route Class
 *
 * @version     1.7.0
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
     * @type    mixed   Prepare function (no-cache)
     * 
     */
    static private $_prepare_function = null;
    
    /**
     * @type    array   Prepare function parameters
     * 
     */
    static private $_prepare_function_params = array();
    
    /**
     * @type    mixed   Clean function (no-cache)
     * 
     */
    static private $_clean_function = null;
    
    /**
     * @type    array   Clean function parameters
     * 
     */
    static private $_clean_function_params = array();

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
    static public function get($path, $callback, $cachable = false, $cache_time = 1440) {
        self::serve('GET', $path, $callback, $cachable, $cache_time);
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
    static public function post($path, $callback, $cachable = false, $cache_time = 1440) {
        self::serve('POST', $path, $callback, $cachable, $cache_time);
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
    static public function put($path, $callback, $cachable = false, $cache_time = 1440) {
        self::serve('PUT', $path, $callback, $cachable, $cache_time);
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
    static public function delete($path, $callback, $cachable = false, $cache_time = 1440) {
        self::serve('DELETE', $path, $callback, $cachable, $cache_time);
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
    static public function all($path, $callback, $cachable = false, $cache_time = 1440) {
        self::serve('', $path, $callback, $cachable, $cache_time);
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
    static public function serve($method, $path, $callback, $cachable = false, $cache_time = 1440) {
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
     * Prepare function
     * 
     * @param   function    $callback   Set prepare callback
     * @param   array       $params     Parameters to be sent
     * @return  void
     * 
     */
    static public function prepare($callback, array $params = array()) {
        if (!is_callable($callback)) {
            \Lollipop\Exception::error('Invalid prepare callback');
        }
        
        self::$_prepare_function = $callback;
        self::$_prepare_function_params = $params;
    }
    
    /**
     * Clean function
     * 
     * @param   function    $callback   Set clean callback
     * @param   array       $params     Parameters to be sent
     * 
     * @return  void
     * 
     */
    static public function clean($callback, array $params = array()) {
        if (!is_callable($callback)) {
            \Lollipop\Exception::error('Invalid clean callback');
        }
        
        self::$_clean_function = $callback;
        self::$_clean_function_params = $params;
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

            return self::_callback($callback, $params);
        } else {
            self::$_is_listening = false;
            self::_checkNotFound();
        }
    }
    
    /**
     * Prepare function
     * 
     * @return  mixed
     * 
     */
    static private function _prepare() {
        ob_start();
        $o = self::$_prepare_function && is_callable(self::$_prepare_function) ? call_user_func_array(self::$_prepare_function, self::$_prepare_function_params) : null;
        ob_get_clean();
        
        return $o;
    }
    
    /**
     * Clean function
     * 
     * @return  mixed
     * 
     */
    static private function _clean() {
        ob_start();
        $o = self::$_clean_function && is_callable(self::$_clean_function) ? call_user_func_array(self::$_clean_function, self::$_clean_function_params) : null;
        ob_get_clean();
        
        return $o;
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
                $cache_time = isset($route['cache_time']) ? $route['cache_time'] : 1440;
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
                        // Call prepare function used by programmer
                        self::_prepare();
                    
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

                            // Call clean function
                            self::_clean();

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
                        \Lollipop\Log::error('Dispatcher is already running. Can\'t run multiple routes.');
                    }
                    
                    // Call clean function
                    self::_clean();

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
            
            $output = null;
            
            switch (count($ctoks)) {
                case 1: // Function only
                    if (!function_exists($action = $ctoks[0])) {
                        \Lollipop\Log::error('Callback is not a function', true);
                    }
                    
                    ob_start();
                    $output = call_user_func_array($action, $args); // Update callback
                    ob_get_clean();
                    
                    return $output;
                    
                    break;
                case 2: // Controller and Action
                    if (class_exists($ctoks[0]) &&
                        is_callable(array($controller = new $ctoks[0], $action = $ctoks[1]))) {
                        
                        ob_start();
                        $output = call_user_func_array(array($controller, $action), $args);
                        ob_get_clean();
                        
                        return $output;
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
            ob_start();
            $output = call_user_func_array($callback, $args); // Return anonymous function
            ob_get_clean();
        }
        
        // Automatically return null from everything that is not an string or callable
        return $output;
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
        
        $output = '';
        $output_config = \Lollipop\Config::get('output');
        $output_compression = !is_null($output_config) && isset($output_config->compression) && $output_config->compression;
        $output_callback_function = '';
        
        // If data is in array format then set content-type
        // to application/json
        if (is_array($data) || is_object($data)) {
            self::setHeader('Content-type: application/json');
            // Convert to json
            $output_callback_function = json_encode($data);
        } else {
            // Default
            $output_callback_function = $data;
        }
        
        $output = $output_callback_function;
        
        if ($output_compression) {
            // Set Content coding a gzip
            self::setHeader('Content-Encoding: gzip');
            
            // Set headers for gzip
            $output = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $output .= gzcompress($output_callback_function);
        }
        
        return $output;
    }

    /**
     * Check if any of routes doesn't match
     *
     * @return  void
     *
     */
    static private function _checkNotFound() {
        if (!self::$_is_listening && (\Lollipop\Config::get('show_not_found') === null || \Lollipop\Config::get('show_not_found') !== false)) {
            \Lollipop\Log::notice('404 Not Found: ' . $_SERVER['REQUEST_URI']);

            header('HTTP/1.0 404 Not Found');

            if (\Lollipop\Config::get('not_found_page')) {
                echo self::_returnData(self::forward(\Lollipop\Config::get('not_found_page')));
            } else {
                $pagenotfound = '<!DOCTYPE html>';
                $pagenotfound .= '<!-- Lollipop for PHP by John Aldrich Bernardo -->';
                $pagenotfound .= '<html>';
                $pagenotfound .= '<head><title>404 Not Found</title></head>';
                $pagenotfound .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
                $pagenotfound .= '<body>';
                $pagenotfound .= '<h1>404 Not Found</h1>';
                $pagenotfound .= '<p>The page that you have requested could not be found.</p>';
                $pagenotfound .= '</body>';
                $pagenotfound .= '</html>';

                echo self::_returnData($pagenotfound);
            }
            
            // Call clean function
            self::_clean();

            exit;
        }
    }
}

?>
