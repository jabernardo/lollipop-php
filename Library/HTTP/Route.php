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
use \Lollipop\HTTP\Request;

/**
 * Lollipop Route Class
 *
 * @version     3.0.2
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
    static private $_stored_routes = [];

    /**
     * @var    bool     Is route forwarded?
     * 
     */
    static private $_is_forwarded = false;

    /**
     * @var mixed   Prepare callback
     * 
     */
    static private $_prepare = [];

    /**
     * @var mixed   Clean callback
     * 
     */
    static private $_clean = [];

    /**
     * @var array   Active route
     * 
     */
    static private $_active_route = [];

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
        self::serve([
            'path' => $path,
            'method' => ['GET'],
            'callback' => $callback,
            'cachable' => $cachable,
            'cache_time' => $cache_time
        ]);
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
        self::serve([
            'path' => $path,
            'method' => ['POST'],
            'callback' => $callback,
            'cachable' => $cachable,
            'cache_time' => $cache_time
        ]);
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
        self::serve([
            'path' => $path,
            'method' => ['PUT'],
            'callback' => $callback,
            'cachable' => $cachable,
            'cache_time' => $cache_time
        ]);
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
        self::serve([
            'path' => $path,
            'method' => ['DELETE'],
            'callback' => $callback,
            'cachable' => $cachable,
            'cache_time' => $cache_time
        ]);
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
        self::serve([
            'path' => $path,
            'method' => ['PATCH'],
            'callback' => $callback,
            'cachable' => $cachable,
            'cache_time' => $cache_time
        ]);
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
        self::serve([
            'path' => $path,
            'method' => [],
            'callback' => $callback,
            'cachable' => $cachable,
            'cache_time' => $cache_time
        ]);
    }

    /**
     * Serve route
     *
     * @param   array   $route  Route settings
     * @example
     * 
     *      [
     *          'path' => '/',
     *          'callback' => 'MyController.index',
     *          'method' => ['GET', 'POST'],
     *          'before' => ['MiddleWare1', 'MiddleWare2'],
     *          'after' => ['MiddleWare3', function(Response $res, $args)]
     *      ]
     * 
     * @return  void
     *
     */
    static public function serve($route) {
        // Default middlewares before
        $middlewares_before = [
                    '\\Lollipop\\HTTP\\Middleware\\AntiCsrf'
                ];
        
        // Default path to '/'
        $path = fuse($route['path'], '');
        $path = trim($path, '/');

        // Store route
        self::$_stored_routes[$path] = [
            'method' => fuse($route['method'], []),
            'callback' => fuse($route['callback'], function() {}),
            'cachable' => fuse($route['cachable'], false),
            'cache_time' => fuse($route['cache_time'], 1440),
            'before' => fuse($route['before'], $middlewares_before),
            'after' => fuse($route['after'], [])
        ];

        // Register dispatcher once this function was called
        self::_registerDispatch();
    }
    
    /**
     * Route forwarding
     *
     * @access  public
     * @param   string                  $path   Route
     * @param   \Lollipop\HTTP\Request  $req    Request object
     * @param   \Lollipop\HTTP\Response $res    Response object
     * @param   array                   $params Arguments
     *
     */
    static public function forward($path, \Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res, array $params = []) {
        self::$_is_forwarded = true;
        
        // Check if landing route is declared
        // we don't want to go in a road without signs
        if (isset(self::$_stored_routes[$path])) {
            $callback = self::$_stored_routes[$path];
            $callback = $callback['callback'];

            // Call back requires
            return self::_callback($callback, $req, $res, $params);
        }
        
        // Create an empty response once 
        // route wasn't found
        return new Response();
    }

    /**
     * Get routes 
     * 
     * @access  public
     * @return  array
     * 
     */
    static public function getRoutes() {
        // Return all routes stored
        return self::$_stored_routes;
    }
    
    /**
     * Get active route information
     * 
     * @access  public
     * @return  array
     * 
     */
    static public function getActiveRoute() {
        return self::$_active_route;
    }

    /**
     * Dispath function
     *
     * @access  private
     * @return  void
     *
     */
    static private function _dispatch() {
        $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $parser = new \Lollipop\HTTP\URL\Parser($url);

        foreach (self::$_stored_routes as $path => $route) {
            // Callback for route
            $callback = fuse($route['callback'], function(){});
            // Check if route or url is cachable (defaults to false)
            $cachable = fuse($route['cachable'], false);
            // Cache time
            $cache_time = fuse($route['cache_time'], 1440);
        
            // Check if request method matchess
            $request_method = isset($route['method']) ? $route['method'] : [];
            
            if (is_array($request_method)) {
                // Make sure all request methods are in uppercase
                // Most of servers are configured with uppercase
                $request_method = array_map('strtoupper', $request_method);
            }

            $rest_test = is_array($request_method) && 
            (in_array($_SERVER['REQUEST_METHOD'], $request_method) || count($request_method) === 0);

            if ($rest_test && $parser->test($path)) {
                $matches = $parser->getMatches();
                // Cache key
                $cache_key = $path;

                if ($matches) {
                    // Make sure cache keys are unique by using parameters
                    // sent to it
                    $cache_key .= '|' . implode(',', $matches);
                }
                
                // If page is not cacheable make sure to remove existing keys
                if (!$cachable) {
                    Cache::remove($cache_key);
                }
                
                // Set route as active
                self::$_active_route = [ $path => $route ];

                // Create a new response
                $response = new Response();
                // New request object
                $request = new Request();

                // Now call the main callback
                $response = self::_callback($callback, $request, $response, $matches);
                
                // Forwarded header
                if (self::$_is_forwarded) {
                    $response->header('lollipop-forwarded: true');
                }

                // Is gzip compression enabled in config
                if (Config::get('output.compression')) {
                    $response->compress();
                }

                // Is gzip compression requested: `lollipop-gzip`, this will override config
                $req = new Request();
                $gzip_header = $req->header('lollipop-gzip');
                
                if (!is_null($gzip_header)) {
                    $response->compress(!strcmp($gzip_header, 'true'));
                }

                return $response;
            }
        }
        
        return new Response();
    }

    /**
     * Call callback and return data
     *
     * @access  private
     * @param   mixed   $callback   (string or callable) string must be {abc}.{abc} format to use controller action
     * @param   array   $args       Parameters to be passed to callback
     * @return  mixed
     *
     */
    static private function _callback($callback, \Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res, array $args = []) {        
        if (is_string($callback)) {
            // If callback was string then
            // Explode it by (dot) to determine the Controller and Action
            $ctoks = explode('.', $callback);
            
            $output = '';
            
            switch (count($ctoks)) {
                case 1: // Function only
                    if (!function_exists($action = $ctoks[0])) {
                        Log::error('Callback is not a function', true);
                    }
                    
                    ob_start();
                    $output = call_user_func($action, $req, $res, $args); // Update callback
                    ob_get_clean();
                    
                    break;
                case 2: // Controller and Action
                    if (class_exists($ctoks[0]) &&
                        is_callable([ $controller = new $ctoks[0], $action = $ctoks[1] ])) {
                        
                        ob_start();
                        $output = call_user_func([ $controller, $action ], $req, $res, $args);
                        ob_get_clean();
                    } else {
                        Log::error('Can\'t find controller and action', true);
                    }
                
                    break;
                
                default: // Invalid callback
                    Log::error('Callback is not a function', true);
                    
                    break;
            }
        }
        
        // Only if sent parameter is callable
        if (is_callable($callback)) {
            ob_start();
            $output = call_user_func($callback, $req, $res, $args); // Return anonymous function
            ob_get_clean();
        }
        
        // Set response object
        if ($output instanceof Response) {
            $res = $output;
        } else {
            $res->set($output);
        }
        
        return $res;
    }

    /**
     * Register dispatch function on shutdown
     *
     * @access  private
     * @return  void
     *
     */
    static private function _registerDispatch() {
        // Register dispatch function
        if (!self::$_dispatch_registered) {
            // Make sure things ends here...
            register_shutdown_function(function() {
                // Get response data from Dispatcher
                $response = self::_dispatch();
                
                // Check if active route is not set
                // and `page_not_found.show` configuration was `true`
                if (empty(self::getActiveRoute()) &&
                    Config::get('page_not_found.show', true)) {
                    // Get 404 page from _checkNotFound function
                    $response = self::_checkNotFound();
                }
                
                // Render output from our application
                if ($response instanceof Response) {
                    // `->render()` will set cookies, header and document
                    // content
                    $response->render();
                }
            });
            
            // Mark as dispatched
            self::$_dispatch_registered = true;
        }
    }

    /**
     * Check if any of routes doesn't match
     *
     * @access  private
     * @return  \Lollipop\HTTP\Response
     *
     */
    static private function _checkNotFound() {
        // Create a default 404 page
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
        // Request object
        $request = new Request();
        
        // Check if 404 pages are re-routed
        // via configuration
        if ($page_route = Config::get('page_not_found.route')) {
            // Get route information in stored routes
            $route_info = fuse(self::$_stored_routes[$page_route], []);
            
            // Forwarding 404 Pages
            $data = self::forward(Config::get('page_not_found.route'), $request, $response);
            
            if ($data instanceof Response) {
                $response = $data;
            } else {
                $response->set($data);
            }
        }

        // Execute
        return $response;
    }
}

?>
