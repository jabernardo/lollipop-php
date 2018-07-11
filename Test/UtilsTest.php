<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Utils;
use \PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testVars() {
        // Fuse
        $this->assertEquals(
            'hello',
            Utils::fuse($undefined, 'hello')
        );

        // Spare
        $this->assertEquals(
            'world',
            Utils::spare(null, 'world')
        );

        // Spare Nan
        $this->assertFalse(
            Utils::spareNan(false, true)
        );

        // Get var
        $test = null;
        $test2 = '!';

        $this->assertFalse(
            Utils::getVar($test)
        );

        $this->assertEquals(
            '!',
            Utils::getVar($test2)
        );
    }

    public function testCollection() {
        $arr1 = [
            'key' => 'val'
        ];

        $arr2 = [
            3 => 'test'
        ];

        // Array merge
        $this->assertEquals(
            Utils::arrayMerge($arr1, $arr2),
            [
                'key' => 'val',
                '3' => 'test'
            ]
        );
    }
}
