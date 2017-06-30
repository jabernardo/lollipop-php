<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Cache;
use \PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testCache() {
        Cache::save('message', 'hello');
        
        $this->assertEquals(
                'hello',
                Cache::recover('message')
            );
    }
    
    public function testRemove() {
        $this->assertEquals(
                true,
                Cache::remove('message')
            );
    }
    
    public function testExists() {
        $this->assertEquals(
                false,
                Cache::exists('message')
            );
    }
    
    public function testPurge() {
        Cache::save('message', 'hello');
        Cache::purge();
        
        $this->assertEquals(
                false,
                Cache::exists('message')
            );
    }
}
