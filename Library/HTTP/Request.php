<?php

namespace Lollipop\HTTP;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\HTTP\Cookie;

/**
 * Request Class 
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for get and post request
 * 
 */
class Request
{
    /**
     * Simple Curl Wrapper Trait
     * 
     * 
     */
    use \Lollipop\HTTP\CurlTrait;
    
    /**
     * @access  private
     * @var     array   Centralized session requests
     * 
     */
    private $_all_requests = [];
    
    /**
     * @access  private
     * @var     array   Queries
     * 
     */
    private $_all_queries = [];
    
    /**
     * @access  private
     * @var     string  Request method
     * 
     */
    private $_method = 'GET';
    
    /**
     * Class construct
     * 
     */
    function __construct() {
        // Also support PUT and DELETE
        parse_str(file_get_contents("php://input"), $_php_request);
        // Merge with POST and GET
        $this->_all_requests = array_merge($this->_all_requests, array_merge($_POST, $_php_request));
        
        // Get url queries
        $this->_all_queries = $_GET;
        
        // Request method
        $this->_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }
    
    /**
     * Get input data
     * 
     * @access  public
     * @param   string  $name   Input name
     * @param   string  $val    Default value if input doesn't exists
     * @return  mixed
     * 
     */
    public function input($name, $val = null) {
        return isset($this->_all_requests[$name]) ?
                    $this->_all_requests[$name] :
                    $val;
    }
    
    /**
     * Get query string from url
     * 
     * @access  public
     * @param   string  $name   Input name
     * @param   string  $val    Default value if query doesn't exists
     * @return  mixed
     * 
     */
    public function query($name, $val = null) {
        return isset($this->_all_queries[$name]) ?
                    $this->_all_queries[$name] :
                    $val;
    }
    
    /**
     * Getting segments of inputs
     * 
     * @access  public
     * @param   array   $name   Input names
     * @return  array
     * 
     */
    public function only(array $name = []) {
        $var = [];
        
        foreach ($name as $in) {
            $var[$in] = isset($this->_all_requests[$var]) ? 
                $this->_all_requests[$var] :
                null;
        }
        
        return $var;
    }
    
    /**
     * Get data input except some
     * 
     * @access  public
     * @param   array   $name   Input names
     * @return  array
     * 
     */
    public function except(array $name = []) {
        $var = [];
        
        foreach ($this->_all_requests as $k => $v) {
            if (!in_array($k, $name)) {
                $var[$k] = $v;
            }
        }
        
        return $var;
    }
    
    /**
     * Check if input is received
     * 
     * @access  public
     * @param   string  $name   Input name
     * @return  bool
     * 
     */
    public function has($name) {
        return isset($this->_all_requests[$name]);
    }
    
    /**
     * Check if query is received
     * 
     * @access  public
     * @param   string  $name   Input name
     * @return  bool
     * 
     */
    public function hasQuery($name) {
        return isset($this->_all_queries[$name]);
    }
    
    /**
     * Check if request method is in use
     * 
     * @access  public
     * @param   string  $method     Request method
     * @return  bool
     * 
     */
    public function isMethod($method) {
        return !strcasecmp($method, $this->_method);
    }
    
    /**
     * Get all request variables
     * 
     * @return  array
     * 
     */
    public function all() {
        return $this->_all_requests;
    }
    
    /**
     * Get request URL
     * 
     * @access  public
     * @param   int     $component  URL parse_url component
     * @return  string
     * 
     */
    public function getURL($component = -1) {
        if ($component > -1) {
            return parse_url($_SERVER['REQUEST_URI'], $component);
        }
        return $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Get request method
     * 
     * @access  public
     * @return  string
     * 
     */
    public function getMethod() {
        return $this->_method;
    }
    
    /**
     * Get request header value
     * 
     * @access  public
     * @param   string  $header     Request header
     * @return  mixed   `null` if header is not set
     * 
     */
    public function header($header) {
        foreach(getallheaders() as $k => $v) {
            if (!strcasecmp($k, $header)) {
                return $v;
            }
        }
        
        return null;
    }
    
    /**
     * Get request cookie value
     * 
     * @access  public
     * @param   string  $name   Cookie name
     * @return  string
     * 
     */
    public function cookie($name) {
        return Cookie::get($name);
    }
    
    /**
     * File Uploads
     * 
     * @access  public
     * @return  object
     * 
     */
    public function file($name) {
        return new \Lollipop\HTTP\Upload($name);
    }
}
