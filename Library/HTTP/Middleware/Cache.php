<?php

namespace Lollipop\HTTP\Middleware;

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
use \Lollipop\HTTP\Response;
use \Lollipop\HTTP\Route;

/**
 * Lollipop Cache Middleware
 *
 * @version     1.0.0
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop
 * 
 */
class Cache
{
    /**
     * Middleware Handler
     * 
     * @access  public
     * @param   \Lollipop\HTTP\Request  $req    Request object
     * @param   \Lollipop\HTTP\Response $res    Response object
     * @return  \Lollipop\HTTP\Response
     * 
     */
    public function __invoke(\Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res, Callable $next) {
        // Get current route information
        $active_route = Route::getActiveRoute();

        // Check if cache is enabled in route
        $cache_enabled = isset($active_route['cache']) && $active_route['cache'];

        // Make a unique key per route
        $params = http_build_query($req->get());
        $cache_key = $active_route['path'] . '/' . implode(',', $active_route['arguments']) . '?' . $params;

        if ($cache_enabled && \Lollipop\Cache::exists($cache_key)) {
            $cached = \Lollipop\Cache::recover($cache_key);

            if (is_object($cached) && $cached instanceof \Lollipop\HTTP\Response) {
                // Make sure that recovered route is a valid serialized response object
                // before throwing it back.
                return $cached;
            }
        }

        // Next stack...
        $res = $next($req, $res);

        if ($cache_enabled) {
            // Cache expiration
            $exp = isset($active_route['cache_time']) ? $active_route['cache_time'] : 1440;
            // Save cache
            \Lollipop\Cache::save($cache_key, $res, true, $exp);
        }

        return $res;
    }
}

?>
