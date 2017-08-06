<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Config;
use \PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testAdd() {
        Config::add('cache', true);
        
        $this->assertEquals(
                true,
                Config::get('cache')
            );
    }

    public function testAddMulti() {
        Config::add('log.enable', true);
        
        $this->assertEquals(
                true,
                Config::get('log')->enable
            );
    }
    
    public function testRemove() {
        Config::add('cache', true);
        Config::remove('cache');
        
        $this->assertEquals(
                null,
                Config::get('cache')
            );
    }

    public function testRemoveMulti() {
        Config::add('log.enable', true);
        Config::remove('log');

        $this->assertEquals(
                null,
                Config::get('log')->enable
            );
    }
}
