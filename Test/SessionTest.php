<?php

use \Lollipop\Session;
use \PHPUnit\Framework\TestCase;

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
    
    public function testDrop() {
        Session::set('message', 'hello');
        Session::drop('message');
        
        $this->assertEquals(
                false,
                Session::exists('message')
            );
    }
}
