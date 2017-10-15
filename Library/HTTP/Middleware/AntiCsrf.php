<?php

namespace Lollipop\HTTP\Middleware;

use \Lollipop\Config;
use \Lollipop\Cookie;
use \Lollipop\CsrfToken;
use \Lollipop\HTTP\Response;
use \Lollipop\HTTP\Request;
use \Lollipop\HTTP\Route;

class AntiCsrf
{
    public function handle(Request $req, Response $res, $args) {
        $acsrf_enable = spare(Config::get('anti_csrf.enable'), true);
        $acsrf_name = CsrfToken::getName();
        $expiration = spare(Config::get('anti_csrf.expiration'), 18000);
        
        // Create a cookie for front end use
        Cookie::set($acsrf_name, CsrfToken::get(), '/', $expiration);
        
        if (isset($_POST) && count($_POST) && $acsrf_enable) {
            //var_dump(!Request::get($acsrf_name) || !CsrfToken::isValid(Request::get($acsrf_name)));exit;
            if (!Request::get($acsrf_name) || !CsrfToken::isValid(Request::get($acsrf_name))) {
                //if ($die) {
                
                    self::_kill()->render();exit;
                //}
                
                //return false;
            }
        }

       return new \Lollipop\HTTP\Response('Hello');
    }
    
    
    /**
     * Self killed
     * 
     * 
     */
    private static function _kill() {
        $net = '<!DOCTYPE html>';
        $net .= '<!-- Lollipop for PHP by John Aldrich Bernardo -->';
        $net .= '<html>';
        $net .= '<head><title>Not Enough Tokens</title></head>';
        $net .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $net .= '<body>';
        $net .= '<h1>Not Enough Tokens</h1>';
        $net .= '<p>Oops! Make sure you have enough tokens before you can play.</p>';
        $net .= '</body>';
        $net .= '</html>';
        
        $output = $net;
        $output_config = Config::get('output');
        $output_compression = !is_null($output_config) && isset($output_config->compression) && $output_config->compression;
        
        return new \Lollipop\HTTP\Response($output);
        
        //exit;
    }
}

?>
