<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

use \Lollipop\SQL\Builder as SQLBuilder;
use \Lollipop\SQL\Connection\MySQL as MySQLDatabase;
use \PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testObject() {
        $db = SQLBuilder::table('messages')
            ->where('id', 1)
            ->selectAll();
            
        $this->assertEquals(
                'SELECT * FROM messages WHERE id = \'1\'',
                (string)$db
            );
    }

    public function testObjectConnection() {
        $db = MySQLDatabase::table('messages')
            ->where('id', 1)
            ->selectAll();
        
        $this->assertEquals(
                'SELECT * FROM messages WHERE id = \'1\'',
                (string)$db
            );
    }
}
