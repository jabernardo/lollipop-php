<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Session;
use \PHPUnit\Framework\TestCase;

session_start();

class SessionTest extends TestCase
{
    public function testSetGet() {
        Session::set('message', 'hello');
        
        $this->assertEquals(
                'hello',
                Session::get('message')
            );
    }
    
    public function testExists() {
        Session::set('message', 'hello');
        
        $this->assertEquals(
                true,
                Session::exists('message')
            );
    }
    
    public function testRemove() {
        Session::set('message', 'hello');
        Session::remove('message');
        
        $this->assertEquals(
                false,
                Session::exists('message')
            );
    }
}
