<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Text;
use \Lollipop\Text\Inflector;
use \PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public function testLock() {
        $locked = Text::lock('hello', 12345);
        
        $this->assertEquals(
                'hello',
                Text::unlock($locked, 12345)
            );
    }

    public function testRepeat() {
        $this->assertEquals(
            Text::repeat('h', 3),
            'hhh'
        );
    }

    public function testCensor() {
        $this->assertEquals(
            Inflector::censor('fuck you', ['Fuck'], 'beep'),
            'beep you'
        );
    }

    public function testCamelCase() {
        $this->assertEquals(
            Inflector::camelize('Hello World'),
            'helloWorld'
        );
    }

    public function testStudlyCase() {
        $this->assertEquals(
            Inflector::studly('Hello World'),
            'HelloWorld'
        );
    }
}
