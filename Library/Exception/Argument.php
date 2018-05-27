<?php

namespace Lollipop\Exception;

/**
 * Error 101: Argument Exception
 * 
 * @author John Aldrich Bernardo <4ldrich@protonmail.com>
 * 
 */
class Argument extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 101, Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
