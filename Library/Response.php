<?php

namespace Lollipop;

// @todo clean this up

class Response
{
    private $_headers = array();
    private $_data = '';
    
    function __construct($data = '') {
        $this->_data = $data;
    }
    
    /**
     * Set header
     *
     * @param   mixed    $headers    HTTP header
     * @return  void
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
    }
    

    /**
     * Return string value for data
     *
     * @param   object  $data   Data to convert
     *
     * @return  string
     *
     */
    private function _format($data) {
        $output = '';
        $output_config = Config::get('output');
        $output_compression = !is_null($output_config) && isset($output_config->compression) && $output_config->compression;
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
        
        // Request header to force gzip
        $force_gzip = false;
        
        foreach(getallheaders() as $k => $v) {
            // `lollipop-gzip` is the key, and allowed value is `true`
            if (!strcasecmp($k, 'lollipop-gzip') &&
                !strcasecmp($v, 'true')) {
                    $force_gzip = true;
                }
        }
        
        if ($output_compression || $force_gzip) {
            // Set Content coding a gzip
            $this->header('Content-Encoding: gzip');
            
            // Set headers for gzip
            $output = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $output .= gzcompress($output_callback_function);
        }
        
        return $output;
    }
    
    public function set($data) {
        $this->_data = $data;
    }
    
    public function get() {
        return $this->_format($this->_data);
    }
    
    public function render() {
        foreach ($this->_headers as $header) {
            header($header);
        }
        
        print($this->get());
    }
}
