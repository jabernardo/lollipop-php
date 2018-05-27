<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Cache;
use \Lollipop\Config;
use \PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testCache() {
        Cache::save('message', 'hello');
        
        $this->assertEquals(
                'hello',
                Cache::get('message')
            );
    }

    public function testRemove() {
        Cache::save('message', 'hello');

        $this->assertEquals(
                true,
                Cache::remove('message')
            );
    }

    public function testExists() {
        Cache::save('message', 'hello');
        Cache::remove('message');

        $this->assertEquals(
                false,
                Cache::exists('message')
            );
    }

    public function testPurge() {
        Cache::save('message', 'hello');
        Cache::save('name', 'Aldrich');
        Cache::purge();
        
        $this->assertEquals(
                false,
                Cache::exists('message') && Cache::exists('message')
            );
    }
}
