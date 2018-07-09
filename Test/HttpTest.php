<?php

if (file_exists('PHPUnit/Autoload.php'))
    require_once('PHPUnit/Autoload.php');

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

// test compatibility
if (!function_exists('getallheaders')) {
    function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
    }
}

use \Lollipop\Text;
use \PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    public function testRun() {
        $_SERVER = [];
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SCRIPT_NAME'] = 'index.php';
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        
        \Lollipop\Config::set('router.auto_dispatch', false);

        \Lollipop\HTTP\Router::get('/', function(\Lollipop\HTTP\Request $req, \Lollipop\HTTP\Response $res, array $args = []) {
            $res->set('Hello World!');

            return $res;
        });

        $res = \Lollipop\HTTP\Router::dispatch(false);

        $this->assertEquals($res->get(), 'Hello World!');
    }
}
