<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Number;
use \PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    public function testNum() {
        // Between
        $this->assertFalse(Number::between(10, 0, 5));
        $this->assertTrue(Number::between(8, 6, 10));

        // currency
        $this->assertEquals(
            chr(Number::CURRENCY_PESO) . ' 5,000.000',
            Number::currency('5000', 3)
        );

        // fibonacci
        $this->assertEquals(8, Number::fibonacci(5));

        // factorial
        $this->assertEquals(6, Number::factorial(3));

        // parsable
        $this->assertFalse(Number::parsable('asd2332'));
        $this->assertFalse(Number::parsable('3232dd'));

        // percentage
        $this->assertEquals(
            '10%',
            Number::percentage(10)
        );

        // readable size
        $this->assertEquals(
            Number::readableSize(2048),
            '2 KB'
        );
    }
}

