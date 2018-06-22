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

use \Lollipop\Config;
use \Lollipop\HTTP\Route;
use \Lollipop\HTTP\Response;
use \Lollipop\HTTP\Request;
use \Lollipop\Utils;

/**
 * Lollipop Router Class
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop
 * 
 */
class Router
{
    /**
     * @var     bool        Is Dispatch function already registered on shutdown?
     *
     */
    static private $_dispatch_registered = false;

    /**
     * @var     array       Stored callbacks
     *
     */
    static private $_stored_routes = [];
    /**
     * @var     array       Active route
     * 
     */
    static private $_active_route = null;

    /**
     * @var     Callable    Top callable function
     * 
     */
    static private $_kernel = null;
    
    /**
     * @var     boolean     Is busy dequeueing
     * 
     */
    static private $_busy = false;

    /**
     * @var     array       
     * 
     */
    static private $_middlewares = [];

    /**
     * GET route
     *
     * @param   string      $path       Route
     * @param   function    $callback   Callback function
     * @return  void
     *
     */
    static public function get($path, $callback) {
        self::serve([
            'path' => $path,
            'method' => ['GET'],
            'callback' => $callback
        ]);
    }

    /**
     * POST route
     *
     * @param   string      $path       Route
     * @param   function    $callback   Callback function
     * @return  void
     *
     */
    static public function post($path, $callback) {
        self::serve([
            'path' => $path,
            'method' => ['POST'],
            'callback' => $callback
        ]);
    }

    /**
     * PUT route
     *
     * @param   string      $path       Route
     * @param   function    $callback   Callback function
     * @return  void
     *
     */
    static public function put($path, $callback) {
        self::serve([
            'path' => $path,
            'method' => ['PUT'],
            'callback' => $callback
        ]);
    }

    /**
     * DELETE route
     *
     * @param   string      $path       Route
     * @param   function    $callback   Callback function
     * @return  void
     *
     */
    static public function delete($path, $callback) {
        self::serve([
            'path' => $path,
            'method' => ['DELETE'],
            'callback' => $callback
        ]);
    }

    /**
     * PATCH route
     *
     * @param   string      $path       Route
     * @param   function    $callback   Callback function
     * @return  void
     *
     */
    static public function patch($path, $callback) {
        self::serve([
            'path' => $path,
            'method' => ['PATCH'],
            'callback' => $callback
        ]);
    }

    /**
     * ALL route
     *
     * @param   string      $path       Route
     * @param   function    $callback   Callback function
     * @return  void
     *
     */
    static public function all($path, $callback) {
        self::serve([
            'path' => $path,
            'method' => [],
            'callback' => $callback
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
     *      ]
     * 
     * @throws \Lollipop\Exception\HTTP\Router
     * @return  void
     *
     */
    static public function serve(array $route) {
        if (!isset($route['path']) || !isset($route['callback']) || 
            (isset($route['method']) && is_string($route['method']))) {
            throw new \Lollipop\Exception\HTTP\Router('Invalid route');
        }
        
        // Default path to '/'
        $path = Utils::fuse($route['path'], '');
        $path = trim($path, '/');

        if (!isset($route['method'])) {
            $route['method'] = [];
        }
        
        if (!isset($route['middlewares'])) {
            $route['middlewares'] = [];
        }
        
        if (!isset($route['arguments'])) {
            $route['arguments'] = [];
        }
        
        // Report duplicated path
        if (isset(self::$_stored_routes[$path]))
            throw new \Lollipop\Exception\HTTP\Router("Duplicate path: $path");

        // Store route
        self::$_stored_routes[$path] = $route;

        // Register dispatcher once this function was called
        self::_registerDispatch();
    }
    
    /**
     * Add middleware
     * 
     * @access  public
     * @param   Callable    $callback   Middleware callable
     * @return  void
     * 
     */
    static public function addMiddleware(Callable $callback) {
        self::$_middlewares[] = $callback;
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
     * @access  public
     * @param   boolean $render     Render response (true)
     * @return  void
     *
     */
    static public function dispatch($render = true) {
        // Get URL Path property only
        $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        
        // Create an instance of URL parser for checking if current
        // path matches any route
        $parser = new \Lollipop\HTTP\URL\Pretty($url);
        
        // Get 404 Page Not Found
        // Check if `404` route was declared
        self::$_active_route = isset(self::$_stored_routes['404']) ?
            self::$_stored_routes['404'] :
            self::_getDefaultPageNotFound();
        
        // Create a new response
        $response = new Response();
        // New request object
        $request = new Request();

        foreach (self::$_stored_routes as $path => $route) {
            // Callback for route
            $callback = Utils::fuse($route['callback'], function(){});
            // Check if request method matchess
            $request_method = isset($route['method']) ? $route['method'] : [];
            
            if (is_array($request_method)) {
                // Make sure all request methods are in uppercase
                // Most of servers are configured with uppercase
                $request_method = array_map('strtoupper', $request_method);
            }

            // Check if request method matches the expected from route information
            $rest_test = is_array($request_method) && 
            (in_array($_SERVER['REQUEST_METHOD'], $request_method) || count($request_method) === 0);
            
            if ($rest_test && $parser->test($path)) {
                // Set the route arguments based from the matches from the url
                $route['arguments'] = $parser->getMatches();;
                
                // Set route as active
                self::$_active_route = $route;
            }
        }
        
        // Stack specific route level middleware
        if (isset(self::$_active_route['middlewares']) && is_array(self::$_active_route['middlewares'])) {
            foreach (self::$_active_route['middlewares'] as $mw) {
                self::_stackMiddleware($mw);
            }
        }
        
        // Stack route level middleware
        if (isset(self::$_middlewares) && is_array(self::$_middlewares)) {
            foreach (self::$_middlewares as $mw) {
                self::_stackMiddleware($mw);
            }
        }
        
        // Now call the main callback
        $response = self::_process($request, $response);
        
        // Render output from our application
        if (!($response instanceof Response)) {
            $response = new Response($response);
        }
        
        // Is gzip compression enabled in config
        if (Config::get('output.compression')) {
            $response->compress();
        }

        if ($render) {
            // `->render()` will set cookies, header and document
            // content
            $response->render();
        }

        return $response;
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
        $auto_dispatch = !is_null(Config::get('router.auto_dispatch')) ?
            Config::get('router.auto_dispatch') :
            true;
        
        if (!self::$_dispatch_registered && $auto_dispatch) {
            // Make sure things ends here...
            register_shutdown_function(function() {
                // Get response data from Dispatcher
                $response = self::dispatch();
            });
            
            // Mark as dispatched
            self::$_dispatch_registered = true;
        }
    }

    /**
     * Process middleware stack
     * 
     * @access  private
     * @param   \Lollipop\HTTP\Request  $req    Request Object
     * @param   \Lollipop\HTTP\Response $res    Response Object
     * @return  \Lollipop\HTTP\Response Response object
     * 
     */
    static private function _process(\Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res) {
        if (is_null(self::$_kernel)) {
            $active = self::$_active_route;
            $top = function(\Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res) use ($active) {
                return Route::resolve($active['callback'], $req, $res, $active['arguments']);
            };
            self::$_kernel = $top;
        }
        
        // Create a new address for the top callable
        $start = self::$_kernel;
        
        // Start dequeue
        self::$_busy = true;
        $new_response = $start($req, $res);
        self::$_busy = false;
        
        // Just want to make sure that processed response from middlewares
        // are instance of \Calf\HTTP\Response
        if ($new_response instanceof \Lollipop\HTTP\Response) {
            $res = $new_response;
        } else {
            $res->set($new_response);
        }

        return $res;
    }

    /**
     * Stack middleware
     * 
     * @access  private
     * @param   Callable    $callback   Middleware callable
     * @throws  \Lollipop\Exception\HTTP\Router
     * @return  void
     *
     */
    static private function _stackMiddleware(Callable $callback) {
        if (self::$_busy) {
            // Make sure it's not busy before adding something.
            throw new \Lollipop\Exception\HTTP\Router('Cannot add new middleware while dequeue in progress');
        }
        
        if (is_null(self::$_kernel)) {
            $active = self::$_active_route;
            $top = function(\Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res) use ($active) {
                return Route::resolve($active['callback'], $req, $res, $active['arguments']);
            };
            self::$_kernel = $top;
        }

        $next = self::$_kernel;

        // The update the top function
        self::$_kernel = function(\Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res) use ($callback, $next) {
            // Pass the last function
            $new_response = call_user_func($callback, $req, $res, $next);
            
            if ($new_response instanceof \Lollipop\HTTP\Response) {
                $res = $new_response;
            } else {
                $res->set($new_response);
            }
            
            // Return the new result
            return $res;
        };
    }

    /**
     * Check if any of routes doesn't match
     *
     * @access  private
     * @return  \Lollipop\HTTP\Response
     *
     */
    static private function _getDefaultPageNotFound() {
        return [
            'path' => '404',
            'callback' => function(\Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res, $args = []) {
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
            },
            'arguments' => []
        ];
    }
}
