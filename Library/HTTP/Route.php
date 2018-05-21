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

use \Lollipop\Log;
use \Lollipop\HTTP\Response;
use \Lollipop\HTTP\Request;

/**
 * Lollipop Route Class
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop
 * 
 */
class Route
{
    /**
     * Call callback and return data
     *
     * @access  private
     * @param   mixed   $callback   (string or callable) string must be {abc}.{abc} format to use controller action
     * @param   \Lollipop\HTTP\Request  $req    Request Object
     * @param   \Lollipop\HTTP\Response $res    Response Object
     * @param   array   $args       Parameters to be passed to callback
     * @return  \Lollipop\HTTP\Response
     *
     */
    static public function resolve($callback, \Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res, array $args = []) {        
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
            $res = new Response($output);
        }
        
        return $res;
    }
}
