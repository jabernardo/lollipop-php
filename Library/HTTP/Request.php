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
    private $_requests = [];
    
    /**
     * @access  private
     * @var     array   Queries
     * 
     */
    private $_queries = [];
    
    /**
     * @access  private
     * @var     array   Headers
     */
    private $_headers = [];

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
        $this->_requests = array_merge($this->_requests, array_merge($_POST, $_php_request));
        
        // Get url queries
        $this->_queries = $_GET;

        // Headers
        $this->_headers = \getallheaders();
        
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
        return isset($this->_requests[$name]) ?
                    $this->_requests[$name] :
                    $val;
    }
    
    /**
     * Alias only
     * 
     * @access  public
     * @param   array   $names   Input names
     * @param   mixed   $default    Default value (null)
     * @return  array
     * 
     */
    public function inputs(array $names, $val = null) {
        return $this->only($names, $val);
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
        return isset($this->_queries[$name]) ?
                    $this->_queries[$name] :
                    $val;
    }
    
    /**
     * Getting querie from url parameters
     * 
     * @access  public
     * @param   array   $name       Query names
     * @param   mixed   $default    Default value
     * @return  array
     * 
     */
    public function queries(array $names, $default = null) {
        $var = [];
        
        foreach ($names as $in) {
            $var[$in] = isset($this->_queries[$in]) ? 
                $this->_queries[$in] :
                $default;
        }
        
        return $var;
    }
    
    /**
     * Getting segments of inputs
     * 
     * @access  public
     * @param   array   $names   Input names
     * @param   mixed   $default    Default value (null)
     * @return  array
     * 
     */
    public function only(array $names, $default = null) {
        $var = [];
        
        foreach ($names as $in) {
            $var[$in] = isset($this->_requests[$in]) ? 
                $this->_requests[$in] :
                $default;
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
    public function except(array $name) {
        $var = [];
        
        foreach ($this->_requests as $k => $v) {
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
        return isset($this->_requests[$name]);
    }
    
    /**
     * Alias has
     * 
     * @access  public
     * @param   string  $name   Input name
     * @return  bool
     * 
     */
    public function hasInput($name) {
        return $this->has($name);
    }
    
    /**
     * Check if inputs are received
     * 
     * @access  public
     * @param   array   $names  Input names
     * @return  bool
     * 
     */
    public function hasInputs(array $names) {
        foreach ($names as $name) {
            if (!$this->has($name))
                return false;
        }
        
        return true;
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
        return isset($this->_queries[$name]);
    }
    
    /**
     * Check if queries exists
     * 
     * @access  public
     * @param   string  $names   Query names
     * @return  bool
     * 
     */
    public function hasQueries($names) {
        foreach ($names as $name) {
            if (!$this->hasQuery($name))
                return false;
        }
        
        return true;
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
        return $this->_requests;
    }
    
    /**
     * Get request URL
     * 
     * @access  public
     * @param   int     $component  URL parse_url component
     * @return  string
     * 
     */
    public function url($component = -1) {
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
    public function method() {
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
        foreach($this->_headers as $k => $v) {
            if (!strcasecmp($k, $header)) {
                return $v;
            }
        }
        
        return null;
    }

    /**
     * Return all request headers
     *
     * @return array
     */
    public function headers() {
        return $this->_headers();
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
     * Get all request cookies
     *
     * @return array
     */
    public function cookies() {
        return Cookie::getAll();
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
