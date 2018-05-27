<?php

namespace Lollipop\Exception;

/**
 * Error 102: Configuration Exception
 * 
 * @author John Aldrich Bernardo <4ldrich@protonmail.com>
 * 
 */
class Configuration extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 102, Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
