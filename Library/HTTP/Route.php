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
 * @version     3.0.0-RC1
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
     * @var mixed   Prepare callback
     * 
     */
    static private $_prepare = array();

    /**
     * @var mixed   Clean callback
     * 
     */
    static private $_clean = array();

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
                'path' => $path,
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
                'path' => $path,
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
                'path' => $path,
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
                'path' => $path,
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
                'path' => $path,
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
                'path' => $path,
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
        // Default middlewares before
        $middlewares_before = array(
                    '\\Lollipop\\HTTP\\Middleware\\AntiCsrf'
                );
        
        // Default path to '/'
        $path = fuse($route['path'], '');
        
        // Store route
        self::$_stored_routes[$path] = array(
            'method' => fuse($route['method'], ''),
            'callback' => fuse($route['callback'], function() {}),
            'cachable' => fuse($route['cachable'], false),
            'cache_time' => fuse($route['cache_time'], 1440),
            'before' => fuse($route['before'], $middlewares_before),
            'after' => fuse($route['after'], array())
        );

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
    static public function forward($path, \Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res, array $params = array()) {
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
     * Prepare callback. Only one callback is allowed
     * This will lessen unnecessary calls 
     * 
     * @access  public
     * @param   function    $callback   Callback
     * @return  bool        `true` if callback is registered as prepare, else
     *                      `false`
     * 
     */
    static public function prepare($callback) {
        if (is_callable($callback)) {
            // Middle wares only accepts array
            // so we'll set prepare function to be in array format
            self::$_prepare = array($callback); 
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Clean callback. Just like prepare function
     * this clean middleware will only contain one callback
     * 
     * @access  public
     * @param   function    $callback   Callback
     * @return  bool        `true` if callback is registered as prepare, else
     *                      `false`
     * 
     */
    static public function clean($callback) {
        if (is_callable($callback)) {
            self::$_clean = array($callback); 
        }
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
     * Dispath function
     *
     * @access  private
     * @return  void
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

            // Check regex if matching our current path
            $is_match = preg_match('/^' . $path . '$/i', $url, $matches) ||
                        preg_match('/^' . $as . $path . '$/i', $url, $matches);

            // Check if request method matches
            if (isset($route['method'])) {
                $_rm = $route['method'];
                
                if (is_array($_rm)) {
                    // Make sure all request methods are in uppercase
                    $_rm = array_map('strtoupper', $_rm);
                }
                
                // Check if current request method matches our applications
                // preferred request method
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
                    // Make sure cache keys are unique by using parameters
                    // sent to it
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
                        
                        return $page_cache;
                    }
                } else {
                    // Create a new response
                    $response = new Response();
                    // New request object
                    $request = new Request();
                    
                    // Prepare middleware
                    if (count(self::$_prepare)) {
                        $response = self::_middleware(self::$_prepare, $request, $response, $matches);
                    }
                    
                    // Before middlewares
                    if (isset($route['before']) && is_array($route['before']) && count($route['before'])) {
                        $response = self::_middleware($route['before'], $request, $response, $matches);
                    }
                    
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

                    // After middlewares
                    if (isset($route['after']) && is_array($route['after']) && count($route['after'])) {
                        $response = self::_middleware($route['after'], $request, $response, $matches);
                    }

                    // Clean middleware
                    if (count(self::$_clean)) {
                        $response = self::_middleware(self::$_clean, $request, $response, $matches);
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
     * @param   array                   $middlewares    Middle wares
     * @param   \Lollipop\HTTP\Request  $req            Request object
     * @param   \Lollipop\HTTP\Response $res            Response object
     * @param   array                   $args           Arguments
     * @return  \Lollipop\HTTP\Response
     * 
     */
    static private function _middleware(array $middlewares, \Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res, array $args = array()) {
        ob_start();

        // Create a new response onject
        $response = new Response();

        // Add request and response object
        // to parameters the callback will take
        // first parameter should be Request followed by Response
        array_unshift($args, $res);
        array_unshift($args, $req);
        
        foreach ($middlewares as $middleware) {
            if (is_callable($middleware)) {
                // For middlewares declared using anonymous functions
                $output = call_user_func_array($middleware, $args);
            } else if (is_callable(array($middler = new $middleware(), 'handle'))) {
                // Middlewares that uses class
                // make sure middleware class has `handle` function
                // this is the entry point for all middlewares
                $output = call_user_func_array(array($middler, 'handle'), $args);
            }
        }
        
        // Make sure it was a clean return
        // We don't want dumps on the ways
        ob_get_clean();
        
        if ($output instanceof Response) {
            // Middleware returns Response object
            $response = $output;
        } else {
            // Else set Response data from the data middleware sent back
            $response->set($output);
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
    static private function _callback($callback, \Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res, array $args = array()) {
        // Add request and response object
        // to parameters the callback will take
        // first parameter should be Request followed by Response
        array_unshift($args, $res);
        array_unshift($args, $req);
        
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
                
                // Request object for 404 pages
                $request = new Request();
                
                // Check if Response object was carrying empty value
                // and `page_not_found.show` configuration was `true`
                if (!$response->get(true) &&
                    spareNan(Config::get('page_not_found.show'), true)) {
                        // Get 404 page from _checkNotFound function
                    $response = self::_checkNotFound($request, $response);
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
     * @param   \Lollipop\HTTP\Request  $req    Request object
     * @param   \Lollipop\HTTP\Response $res    Response object
     * @return  \Lollipop\HTTP\Response
     *
     */
    static private function _checkNotFound(\Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res) {
        // Check if 404 pages are re-routed
        // via configuration
        if (Config::get('page_not_found.route')) {
            // Forwarding 404 Pages
            $data = self::forward(Config::get('page_not_found.route'), $req, $res);
            
            // Create a new response based from the output of landing 404 page
            $response = new Response();
            
            if ($data instanceof Response) {
                $response = $data;
            } else {
                $response->set($data);
            }
            
            return $response;
        }
        
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
        // Execute
        return $response;
    }
}

?>
