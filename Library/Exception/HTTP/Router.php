<?php

namespace Lollipop\Exception\HTTP;

/**
 * Error 200: Router Exception
 * 
 * @author John Aldrich Bernardo <4ldrich@protonmail.com>
 * 
 */
class Router extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 200, Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
