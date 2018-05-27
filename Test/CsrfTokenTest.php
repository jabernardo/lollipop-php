<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Security\CsrfToken;
use \PHPUnit\Framework\TestCase;

class CsrfTokenTest extends TestCase
{
    public function testToken() {
        $this->assertEquals(
                true,
                CsrfToken::isValid(CsrfToken::get())
            );
    }
}
