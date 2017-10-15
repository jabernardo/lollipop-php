<?php

namespace Lollipop\HTTP;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

/**
 * Check application if running on web server
 * else just terminate
 * 
 */
if (!isset($_SERVER['REQUEST_URI'])) {
    exit('Lollipop Application must be run on a web server.' . PHP_EOL);
}

use \Lollipop\Cache;
use \Lollipop\Config;
use \Lollipop\Log;
use \Lollipop\HTTP\Response;

/**
 * Lollipop Route Class
 *
 * @version     2.0.6
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop
 * @request-headers
 * 
 *      lollipop-gzip: true/false
 *          - Force gzip compression return
 * 
 * @response-headers
 * 
 *      lollipop-forwarded: true/false
 *          - If `Route::forward` is called
 * 
 *      lollipop-cache: true/false
 *          - If page is from cache
 * 
 * @configurations
 * 
 *      page_not_found
 *          route: '/'
 *          show: true/false
 * 
 */
class Route
{
    /**
     * @var     bool    Is Dispatch function already registered on shutdown?
     *
     */
    static private $_dispatch_registered = false;

    /**
     * @var     array   Stored callbacks
     *
     */
    static private $_stored_routes = array();

    /**
     * @var    bool     Is route forwarded?
     * 
     */
    static private $_is_forwarded = false;

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
        self::serve(
                array(
                    'path' => '/',
                    'method' => 'GET',
                    'callback' => $callback,
                    'cachable' => $cachable,
                    'cache_time' => $cache_time
                )
            );
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
        self::serve(
                array(
                    'path' => '/',
                    'method' => 'POST',
                    'callback' => $callback,
                    'cachable' => $cachable,
                    'cache_time' => $cache_time
                )
            );
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
        self::serve(
                array(
                    'path' => '/',
                    'method' => 'PUT',
                    'callback' => $callback,
                    'cachable' => $cachable,
                    'cache_time' => $cache_time
                )
            );
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
        self::serve(
                array(
                    'path' => '/',
                    'method' => 'DELETE',
                    'callback' => $callback,
                    'cachable' => $cachable,
                    'cache_time' => $cache_time
                )
            );
    }

    /**
     * PATCH route
     *
     * @param   string      $path       Route
     * @param   function    $callback   Callback function
     * @param   bool        $cachable   Is page cache enable? (default is false)
     * @param   int         $cache_time Cache time (in minutes 1440 or 24 hrs default)
     *
     * @return  void
     *
     */
    static public function patch($path, $callback, $cachable = false, $cache_time = 1440) {
        self::serve(
                array(
                    'path' => '/',
                    'method' => 'PATCH',
                    'callback' => $callback,
                    'cachable' => $cachable,
                    'cache_time' => $cache_time
                )
            );
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
        self::serve(
                array(
                    'path' => '/',
                    'method' => '',
                    'callback' => $callback,
                    'cachable' => $cachable,
                    'cache_time' => $cache_time
                )
            );
    }

    /**
     * Serve route
     *
     * @param   string      $method     Request method
     * @param   string      $path       Route
     * @param   function    $callback   Callback function
     * @param   bool        $cachable   Is page cache enable? (default is false)
     * @param   int         $cache_time Cache time (in minutes 1440 or 24 hrs default)
     * @example
     * 
     *      [
     *          'path' => '/',
     *          'method' => ['GET', 'POST'],
     *          'before' => ['MiddleWare1', 'MiddleWare2'],
     *          'after' => ['MiddleWare3', function(Response $res, $args)]
     *      ]
     * 
     * @return  void
     *
     */
    static public function serve($route) {
        // Store route
        self::$_stored_routes[fuse($route['path'], '')] = array(
                                            'method' => fuse($route['method'], ''),
                                            'callback' => fuse($route['callback'], function() {}),
                                            'cachable' => fuse($route['cachable'], false),
                                            'cache_time' => fuse($route['cache_time'], 1440),
                                            'before' => fuse($route['before'], array()),
                                            'after' => fuse($route['after'], array())
                                        );

        self::_registerDispatch();
    }
    
    /**
     * Route forwarding
     *
     * @param string $path Route
     * @param array  $params Arguments
     *
     */
    static public function forward($path, array $params = array()) {
        self::$_is_forwarded = true;
        
        if (isset(self::$_stored_routes[$path])) {
            $callback = self::$_stored_routes[$path];
            $callback = $callback['callback'];

            return self::_callback($callback, $params);
        }
        
        return new Response();
    }
    
    /**
     * Get routes 
     * 
     * @return  array
     * 
     */
    static public function getRoutes() {
        return self::$_stored_routes;
    }

    /**
     * Dispath function
     *
     *
     * @return void
     *
     */
    static private function _dispatch() {
        foreach (self::$_stored_routes as $path => $route) {
            // Callback for route
            $callback = fuse($route['callback'], function(){});
            // Check if route or url is cachable (defaults to false)
            $cachable = fuse($route['cachable'], false);
            // Cache time
            $cache_time = fuse($route['cache_time'], 1440);
            // Translate regular expressions
            $path = str_replace(array('(%s)', '(%d)', '(%%)', '/'), array('(\w+)', '(\d+)', '(.*)', '\/'), trim($path, '/'));
            // Request URL
            $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            // Active script or running script (this is when no redirection is being done in .htaccess)
            $as =  str_replace('/', '\/', trim($_SERVER["SCRIPT_NAME"], '/') . ($path ? '/' : ''));

            $is_match = preg_match('/^' . $path . '$/i', $url, $matches) ||
                        preg_match('/^' . $as . $path . '$/i', $url, $matches);

            // Check if request method matches
            if (isset($route['method'])) {
                $_rm = $route['method'];
                
                if (is_array($_rm)) {
                    $_rm = array_map('strtoupper', $_rm);
                }
                
                if ((is_array($_rm) && !in_array($_SERVER['REQUEST_METHOD'], $_rm)) ||
                    (is_string($_rm) && $_rm !== $_SERVER['REQUEST_METHOD'] && $_rm !== '')) {
                    $is_match = false;
                }
            }

            if ($is_match) {
                // Remove unneeded data
                array_shift($matches);

                // Cache key
                $cache_key = $path;

                if ($matches) {
                    $cache_key .= '|' . implode(',', $matches);
                }
                
                // If page is not cacheable make sure to remove existing keys
                if (!$cachable) {
                    Cache::remove($cache_key);
                }

                // Check if page cache is enabled and recover cache if available
                // Also we could use ?nocache as parameter
                // to force caching to be disabled
                if ($cachable && !isset($_REQUEST['nocache']) && Cache::exists($cache_key)) {
                    $page_cache = Cache::recover($cache_key);
                    
                    if (is_object($page_cache) && $page_cache instanceof Response) {
                        $page_cache->header('lollipop-cache: true');
                        $page_cache->render();
                    }
                } else {
                    // Create a new response
                    $response = new Response();
                    
                    if (isset($route['before'])) {
                        $response = self::_middleware($route['before'], $response, $matches);
                    }

                    // Execute callback
                    $data = self::_callback($callback, $matches);
                    
                    if ($data instanceof Response) {
                        $response = $data;
                    } else {
                        $response->set($data);
                    }

                    // Forwarded header
                    if (self::$_is_forwarded) {
                        $response->header('lollipop-forwarded: true');
                    }

                    // Is gzip compression enabled in config
                    if (Config::get('output.compression')) {
                        $response->compress();
                    }

                    // Is gzip compression requested: `lollipop-gzip`, this will override config
                    $gzip_header = self::_getHeader('lollipop-gzip');
                    
                    if ($gzip_header !== false) {
                        if (!strcasecmp($gzip_header, 'true')) {
                            $response->compress();
                        } else {
                            $response->compress(false);
                        }
                    }

                    if (isset($route['after'])) {
                        $response = self::_middleware($route['after'], $response, $matches);
                    }

                    // Save cache
                    if ($cachable && !isset($_REQUEST['nocache'])) {
                        Cache::save($cache_key, $response, false, $cache_time);
                    }
                    
                    // Show output
                    return $response;
                }
            }
        }
        
        return new Response();
    }
    
    /**
     * Middleware
     * 
     * @access  private
     * @param   array   $middlewares    Middle wares
     * @param   \Lollipop\HTTP\Response $response      Response object
     * @return \Lollipop\HTTP\Response
     * 
     */
    static private function _middleware(array $middlewares, Response $response, array $args = array()) {
        foreach ($middlewares as $middleware) {
            if (is_callable($middleware)) {
                $response = $middleware($response, $args);
            } else if (is_callable(array($middler = new $middleware(), 'handle'))) {
                $response = $middler->handle($response, $args);
            }
        }
        
        return $response;
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
                        Log::error('Callback is not a function', true);
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
                    
                    Log::error('Can\'t find controller and action', true);
                
                    break;
                
                default: // Invalid callback
                    Log::error('Callback is not a function', true);
                    
                    break;
            }
        }
        
        // Only if sent parameter is callable
        if (is_callable($callback)) {
            ob_start();
            $output = call_user_func_array($callback, $args); // Return anonymous function
            ob_get_clean();
        }
        
        return $output;
    }
    
    /**
     * Check if request header is set
     * and enabled (true)
     * 
     * @access  public
     * @param   string  $header     Request header
     * @return  mixed
     * 
     */
    static private function _getHeader($header) {
        foreach(getallheaders() as $k => $v) {
            if (!strcasecmp($k, $header)) {
                return $v;
            }
        }
        
        return false;
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
                $response = self::_dispatch();
                
                if (!$response->get(true) &&
                    spareNan(Config::get('page_not_found.show'), true)) {
                    $response = self::_checkNotFound();
                }
                
                $response->render();
            });
            
            // Mark as dispatched
            self::$_dispatch_registered = true;
        }
    }

    /**
     * Check if any of routes doesn't match
     *
     * @return  void
     *
     */
    static private function _checkNotFound() {
        if (Config::get('page_not_found.route')) {
            // Forwarding 404 Pages
            $data = self::forward(Config::get('page_not_found.route'));
            $response = new Response();
            
            if ($data instanceof Response) {
                $response = $data;
            } else {
                $response->set($data);
            }
            
            return $response;
        } else {
            $pagenotfound = '<!DOCTYPE html>'
                    . '<!-- Lollipop for PHP by John Aldrich Bernardo -->'
                    . '<html>'
                    . '<head><title>404 Not Found</title></head>'
                    . '<meta name="viewport" content="width=device-width, initial-scale=1">'
                    . '<body>'
                    . '<h1>404 Not Found</h1>'
                    . '<p>The page that you have requested could not be found.</p>'
                    . '</body>'
                    . '</html>';

            // Create a new 404 Page Not Found Response
            $response = new Response($pagenotfound);
            // Set header for 404
            $response->header('HTTP/1.0 404 Not Found');
            // Execute
            return $response;
        }
        
        return new Response();
    }
}

?>
