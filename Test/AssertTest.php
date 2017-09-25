<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Assert;
use \PHPUnit\Framework\TestCase;

class AssertTest extends TestCase
{
    public function testEqualsTrue() {
        $this->assertTrue(
                Assert::equals('1', 1)
            );
    }
    
    public function testEqualsFalse() {
        $this->assertFalse(
                Assert::equals('a', 1)
            );
    }
    
    public function testStrictEqualsTrue() {
        $this->assertTrue(
                Assert::strictEquals(1, 1)
            );
    }
    
    public function testStrictEqualsFalse() {
        $this->assertFalse(
                Assert::strictEquals(1, '1')
            );
    }
    
    public function testNotEqualsTrue() {
        $this->assertTrue(
                Assert::notEquals(1, 2)
            );
    }
    
    public function testNotEqualsFalse() {
        $this->assertFalse(
                Assert::notEquals('1', 1)
            );
    }
    
    public function testStrictNotEqualsTrue() {
        $this->assertTrue(
                Assert::strictNotEquals(1, '1')
            );
    }
    
    public function testStrictNotEqualsFalse() {
        $this->assertFalse(
                Assert::strictNotEquals(1, 1)
            );
    }
    
    public function testException() {
        function except() {
            throw new \Exception('Some error here.');
        }
        
        $this->assertTrue(
                Assert::exception('except')
            );
    }
    
    public function testNotException() {
        function except2() {
            return 0;
        }
        
        $this->assertTrue(
                Assert::noException('except2')
            );
    }
    
    public function testTrueTrue() {
        $this->assertTrue(
                Assert::true(true)
            );
    }
    
    public function testTrueFalse() {
        $this->assertFalse(
                Assert::true(false)
            );
    }
    
    public function testFalseTrue() {
        $this->assertTrue(
                Assert::false(false)
            );
    }
    
    public function testFalseFalse() {
        $this->assertFalse(
                Assert::false(true)
            );
    }
}
