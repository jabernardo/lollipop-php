<?php

namespace Lollipop\HTTP;

/**
 * Middleware Interface
 * 
 * @version 1.0
 * @author  John Aldrich Bernardo <4ldrich@protonmail.com>
 * 
 */
interface Middleware {
    
    /**
     * All middleware will be required to be a callable
     * so __invoke function is a must so parameters to be instance of Lollipop Router
     * classes
     * 
     * @access  public
     * @param   \Lollipop\HTTP\Request    $request    HTTP Request Object
     * @param   \Lollipop\HTTP\Response   $response   HTTP Response Object
     * @return  \Lollipop\HTTP\Response   Response Object
     * 
     */
    public function __invoke(\Lollipop\HTTP\Request $request, \Lollipop\HTTP\Response $response, callable $next);
    
}
