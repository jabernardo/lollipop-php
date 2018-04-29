<?php

namespace Lollipop\HTTP\Middleware;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\Session;
use \Lollipop\HTTP\Request;
use \Lollipop\HTTP\Response;
use \Lollipop\HTTP\Middleware;

/**
 * Lollipop Gzip Middleware
 *
 * @version     1.0.0
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop
 * 
 */
class Gzip implements Middleware
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
        $res = $next($req, $res);

        if (Config::get('debugger') && !$req->is('disable-debugger')) {
            Session::set('debugger-compress-output', true);
        } else {
            $res->compress(true);
        }

        return $res;
    }
}

?>
