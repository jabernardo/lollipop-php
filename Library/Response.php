<?php

namespace Lollipop;

/**
 * Lollipop Route Class
 *
 * @version     0.1.1-RC1
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop
 * 
 */
class Response
{
    /**
     * @var     array   HTTP Headers
     * 
     */
    private $_headers = array();
    
    /**
     * @var     string  Response data
     * 
     */
    private $_data = '';
    
    /**
     * @var     bool    Compress output using gzip
     * 
     */
    private $_compress = false;
    
    /**
     * Class construct
     * 
     * @access  public
     * @return  object
     * 
     */
    function __construct($data = '') {
        $this->_data = $data;
        
        return $this;
    }

    /**
     * Return string value for data
     *
     * @access  private
     * @param   object  $data   Data to convert
     * @return  string
     *
     */
    private function _format($data) {
        $output_callback_function = '';
        
        // If data is in array format then set content-type
        // to application/json
        if (is_array($data) || is_object($data)) {
            $this->header('Content-type: application/json');
            // Convert to json
            $output_callback_function = json_encode($data);
        } else {
            // Default
            $output_callback_function = $data;
        }
        
        $output = $output_callback_function;
        
        // GZIP output compression
        if ($this->_compress) {
            // Set Content coding a gzip
            $this->header('Content-Encoding: gzip');
            
            // Set headers for gzip
            $output = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $output .= gzcompress($output_callback_function);
        }
        
        return $output;
    }
    
    /**
     * Set Response data
     * 
     * @access  public
     * @param   mixed   $data   New response data
     * @return  object
     * 
     */
    public function set($data) {
        $this->_data = $data;
        
        return $this;
    }
    
    /**
     * Compress output
     * 
     * @access  public
     * @param   bool    $enabled    Enable gzip (default true)
     * @return  object
     * 
     */
    public function compress($enabled = true) {
        $this->_compress = $enabled;
        
        return $this;
    }
    
    /**
     * Get formatted responsed data
     * 
     * @access  public
     * @return  string
     * 
     */
    public function get() {
        return $this->_format($this->_data);
    }
    
    /**
     * Set header
     *
     * @param   mixed    $headers    HTTP header
     * @return  object
     *
     */
    public function header($headers) {
        // Record HTTP header
        if (is_array($headers)) {
            foreach ($headers as $header) {
                array_push($this->_headers, $header);
            }
        } else if (is_string($headers)) {
            array_push($this->_headers, $headers);
        }
        
        return $this;
    }
    
    /**
     * Get headers for response
     * 
     * @access  public
     * @return  array
     * 
     */
    public function getHeaders() {
        return $this->_headers;
    }
    
    /**
     * Set response headers and print response text
     * 
     * @access  public
     * @return  object
     * 
     */
    public function render() {
        foreach ($this->_headers as $header) {
            header($header);
        }
        
        print($this->get());
    }
}
