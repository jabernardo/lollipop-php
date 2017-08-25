<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Tag;
use \PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testTag() {
        $h = Tag::create('p')
            ->add('class', 'error')
            ->add('class', 'log')
            ->add('id', 'message')
            ->contains('Hello')
            ->contains(Tag::create('b')->contains('World!'));
        
        $this->assertEquals(
                (string)$h,
                '<p class="error log" id="message">Hello<b>World!</b></p>'
            );
    }
    
    public function testEmptyTag() {
        $this->assertEquals(
                Tag::create('meta', true)->add('charset', 'utf-8'),
                '<meta charset="utf-8"/>'
            );
    }
}
