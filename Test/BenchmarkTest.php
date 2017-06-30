<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\Benchmark;
use \PHPUnit\Framework\TestCase;

class BenchmarkTest extends TestCase
{
    public function testMark() {
        $this->assertEquals(
                null,
                Benchmark::mark('start')
            );
    }
    
    public function testElapsed() {
        Benchmark::mark('start');
        
        for ($i = 0; $i < 10000; $i++) {
            // do nothing
        }
        
        Benchmark::mark('stop');
        
        $this->assertEquals(
                true,
                is_array(Benchmark::elapsed('start', 'stop'))
            );
    }
    
    public function testElapsedTime() {
        Benchmark::mark('start');
        
        for ($i = 0; $i < 10000; $i++) {
            // do nothing
        }
        
        Benchmark::mark('stop');
        
        $this->assertEquals(
                true,
                is_scalar(Benchmark::elapsedTime('start', 'stop'))
            );
    }
    
    public function testElapsedMemory() {
        Benchmark::mark('start');
        
        for ($i = 0; $i < 10000; $i++) {
            // do nothing
        }
        
        Benchmark::mark('stop');
        
        
        $this->assertEquals(
                true,
                is_scalar(Benchmark::elapsedMemory('start', 'stop'))
            );
    }
}
