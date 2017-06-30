<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Filter;
use \PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function testNameCorrect() {
        $this->assertEquals(
                'John Aldrich Bernardo',
                Filter::name('John Aldrich Bernardo')
            );
    }
    
    public function testNameWrong() {
        $this->assertEquals(
                false,
                Filter::name('asd3r')
            );
    }
    
    public function testContactCorrect() {
        $this->assertEquals(
                '+639751555928',
                Filter::contact('+639751555928')
            );
    }
    
    public function testContactWrong() {
        $this->assertEquals(
                false,
                Filter::contact('ff+d')
            );
    }
    
    public function testEmailCorrect() {
        $this->assertEquals(
                '4ldrich@protonmail.com',
                Filter::email('4ldrich@protonmail.com')
            );
    }
    
    public function testEmailWrong() {
        $this->assertEquals(
                false,
                Filter::email('4ldrich@protonmail')
            );
    }
    
    public function testUrlCorrect() {
        $this->assertEquals(
                'http://aldrich.online',
                Filter::url('http://aldrich.online')
            );
    }
    
    public function testUrlWrong() {
        $this->assertEquals(
                false,
                Filter::url('aldrich.online')
            );
    }
    
    public function testIPCorrect() {
        $this->assertEquals(
                '127.0.0.1',
                Filter::ip('127.0.0.1')
            );
    }
    
    public function testIPWrong() {
        $this->assertEquals(
                false,
                Filter::ip('127.0.0.a')
            );
    }
}
