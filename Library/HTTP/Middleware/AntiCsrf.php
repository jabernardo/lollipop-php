<?php

namespace Lollipop\HTTP\Middleware;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\Cookie;
use \Lollipop\CsrfToken;
use \Lollipop\HTTP\Response;

/**
 * Lollipop AntiCsrf Middleware
 *
 * @version     1.1.1
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop
 * 
 */
class AntiCsrf
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
        $acsrf_enable = Config::get('anti_csrf.enable', true);
        $acsrf_name = CsrfToken::getName();
        $expiration = Config::get('anti_csrf.expiration', 18000);
        
        // Create a cookie for front end use
        Cookie::set($acsrf_name, CsrfToken::get(), '/', $expiration);
        
        if (!$req->isMethod('get') && $acsrf_enable) {
            if (!CsrfToken::isValid($req->header($acsrf_name)) && !CsrfToken::isValid($req->get($acsrf_name))) {
                $output = '<!DOCTYPE html>'
                        . '<!-- Lollipop for PHP by John Aldrich Bernardo -->'
                        . '<html>'
                        . '<head>'
                        . '<title>Not Enough Tokens</title>'
                        . '<meta name="viewport" content="width=device-width, initial-scale=1">'
                        . '</head>'
                        . '<body>'
                        . '<h1>Not Enough Tokens</h1>'
                        . '<p>Oops! Make sure you have enough tokens before you can play.</p>'
                        . '</body>'
                        . '</html>';
                
                $output_config = Config::get('output');
                $output_compression = !is_null($output_config) && isset($output_config->compression) && $output_config->compression;
                
                $res = new Response($output);
                $res->compress($output_compression);
                $res->render();
                exit();
            }
        }
        
        $res = $next($req, $res);

        return $res;
    }
}

?>
