<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\File;
use \PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testFileCreate() {
        $f = new File('sample.txt', true);
        $f->contents('test');
        
        $this->assertEquals(
                true,
                file_exists('sample.txt')
            );
    }
    
    public function testFileSize() {
        $f = new File('sample.txt', true);
        $f->contents('test');
        
        $this->assertEquals(
                true,
                is_numeric($f->size())
            );
        
        if (file_exists('sample.txt')) unlink('sample.txt');
    }
}
