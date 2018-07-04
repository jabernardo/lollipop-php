<?php

namespace Lollipop\HTTP\Middleware;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\HTTP\Response;
use \Lollipop\HTTP\Request;
use \Lollipop\HTTP\Middleware;
use \Lollipop\HTTP\Router;

/**
 * Lollipop Cache Middleware
 * 
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop
 * 
 */
class Cache implements Middleware
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
        $active_route = Router::getActiveRoute();

        // Make a unique key per route
        $params = http_build_query($req->all());
        $cache_key = $active_route['path'] . '/' . implode(',', $active_route['arguments']) . '?' . $params;

        if (\Lollipop\Cache::exists($cache_key)) {
            $cached = \Lollipop\Cache::get($cache_key);

            if (is_object($cached) && $cached instanceof \Lollipop\HTTP\Response) {
                // Make sure that recovered route is a valid serialized response object
                // before throwing it back.
                return $cached;
            }
        }

        // Next stack...
        $res = $next($req, $res);

        // Cache expiration
        $exp = isset($active_route['cache_time']) ? $active_route['cache_time'] : 1440;
        // Save cache
        \Lollipop\Cache::save($cache_key, $res, true, $exp);

        return $res;
    }
}
