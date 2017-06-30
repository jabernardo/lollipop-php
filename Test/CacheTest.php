<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

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
