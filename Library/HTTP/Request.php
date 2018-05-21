<?php

namespace Lollipop\HTTP;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

/**
 * Check application if running on web server
 * else just terminate
 * 
 */
if (!isset($_SERVER['REQUEST_URI'])) {
    exit('Lollipop Application must be run on a web server.' . PHP_EOL);
}

use \Lollipop\Benchmark;
use \Lollipop\Cache;
use \Lollipop\Config;
use \Lollipop\Log;
use \Lollipop\HTTP\Response;

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
     * @access  private
     * @var     array   Centralized session requests
     * 
     */
    private $_all_requests = [];
    
    /**
     * Check for request(s)
     *
     * @param   mixed   $requests   Request names
     *
     * @return bool
     * 
     */
    function is($requests) {
        $is = true;
        
        // Also support PUT and DELETE
        parse_str(file_get_contents("php://input"), $_php_request);
        // Merge with POST and GET
        $this->_all_requests = array_merge($this->_all_requests, array_merge($_REQUEST, $_php_request));
        
        if (is_array($requests)) {
            $returns = [];
            
            foreach ($requests as $request) {
                array_push($returns, isset($this->_all_requests[$request]));
            }
            
            foreach ($returns as $return) {
                if ($return == false) {
                    $is = false;
                }
            }
        } else {
            $is = isset($this->_all_requests[$requests]);
        }
        
        return $is;
    }
    
    /**
     * Check if request method is in use
     * 
     * @access  public
     * @param   string  $method     Request method
     * @return  bool
     * 
     */
    function isMethod($method) {
        return !strcasecmp($method, $_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * Gets values of request(s)
     *
     * @param   array   $requests   Request names
     *
     * @return  array
     * 
     */
    function get($requests = null) {
        $var = [];
        
        // Also support PUT and DELETE
        parse_str(file_get_contents("php://input"), $_php_request);
        // Merge with POST and GET
        $this->_all_requests = array_merge($this->_all_requests, array_merge($_REQUEST, $_php_request));
        
        if (is_array($requests)) {
            foreach ($requests as $request) {
                $var[$request] = isset($this->_all_requests[$request]) ? $this->_all_requests[$request] : null;
            }
        } else if (is_null($requests)) {
            $var = $this->_all_requests;
        } else {
            $var = (isset($this->_all_requests[$requests])) ? $this->_all_requests[$requests] : null;
        }
        
        return $var;
    }
    
    /**
     * Get request method
     * 
     * @access  public
     * @return  string
     * 
     */
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
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
     * Simple cURL Wrapper
     * 
     * @access  public
     * @param   array   $options    Options for request
     * @example
     * 
     *  [
     *      'url' => 'https://hacker-news.firebaseio.com/v0/item/8863.json?print=pretty',
     *      'auth' => [
     *              'user' => 'username',
     *              'pwd' => 'youpassword'
     *          ],
     *      'headers' => [],
     *      'timeout' => 0,
     *      'follow' => true, // Follow redirections
     *      'max-redirections' => 5, // Allowed number of redirections
     *      'cookie-jar' => 'Path to cookie jar',
     *      'cookie-file' => 'Path to cookie file',
     *      'return-headers' => true/false, // Return headers
     *      'no-body' => true/false, // Return no-body
     *      'user-agent' => '', // Custom user agent
     *      'referrer' => '', // HTTP REFERRER
     *      'method' => 'PUT', // Custom method
     *      'parameters' => [ // Parameters
     *              'key' => 'value'
     *          ]
     *  ]
     * 
     * @return  mixed
     * 
     */
    public static function send(array $options) {
        // Get localdb location in config
        $localdb = Config::get('localdb.folder', LOLLIPOP_STORAGE_LOCALDB);
        
        // Request cache
        $request_cache = !is_null(Config::get('request.cache.enable'))
                            ? Config::get('request.cache.enable') 
                            : true;
        
        // Override cache
        $request_cache = isset($options['cache']) ? $options['cache'] : $request_cache;
        
        $request_cache_time = Config::get('request.cache.time', 1440);
        
        // Auto JSON
        $auto_json = Config::get('request.json', true);
        
        // URl is required: CURLOPT_URL
        $url = isset($options['url']) ? $options['url'] : false;
        
        if (!$url) {
            Log::error('URL missing on Request', true);
        }
        
        // Auth: CURLOPT_USERPWD
        $auth_username = isset($options['auth']) && is_array($options['auth']) && isset($options['auth']['user'])
                            ? $options['auth']['user'] : '';
        $auth_passwd = isset($options['auth']) && is_array($options['auth']) && isset($options['auth']['pwd'])
                            ? $options['auth']['pwd'] : '';
        
        $c = curl_init();
        
        if ($auth_username && $auth_passwd) {
            curl_setopt($c, CURLOPT_USERPWD, $auth_username . ':' . $auth_passwd);
        }
        
        curl_setopt($c, CURLOPT_URL, $url);
        // Set empty headers as default: CURLOPT_HTTPHEADER
        curl_setopt($c, CURLOPT_HTTPHEADER, fuse($options['headers'], []));
        // Allow returning of headers
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        // Set timeout 0 as default: CURLOPT_TIMEOUT
        curl_setopt($c, CURLOPT_TIMEOUT, fuse($options['timeout'], 0));
        // Follow URL `true` as default: CURLOPT_FOLLOWLOCATION
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, fuse($options['follow'], true));
        // Set max redirections: CURLOPT_MAXREDIRS
        curl_setopt($c, CURLOPT_MAXREDIRS, fuse($options['max-redirections'], 5));
        // Set cookie jar: CURLOPT_COOKIEJAR
        curl_setopt($c, CURLOPT_COOKIEJAR, fuse($options['cookie-jar'], $localdb . 'cookies'));
        // Set cookie file: CURLOPT_COOKIEFILE
        curl_setopt($c, CURLOPT_COOKIEFILE, fuse($options['cookie-file'], $localdb . 'cookies'));
        // Allow returning of headers
        curl_setopt($c, CURLOPT_HEADER, fuse($options['return-headers'], false));
        // Allow no-body
        curl_setopt($c, CURLOPT_NOBODY, fuse($options['no-body'], false));
        
        if (isset($options['user-agent'])) {
            // User agent
            curl_setopt($c, CURLOPT_USERAGENT, $options['user-agent']);
        }
        
        if (isset($options['referrer'])) {
            // HTTP REFERRER
            curl_setopt($c, CURLOPT_REFERER, $options['referrer']);
        }
        
        if (isset($options['method'])) {
            // Custom Method Request: CURLOPT_CUSTOMREQUEST
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, strtoupper($options['method']));
        }
        
        if (isset($options['parameters'])) {
            // POST parameters
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query(fuse($options['parameters'], [])));
        }
        
        // Get response time
        Benchmark::mark('curl_start');
        // from 
        $response = '';
        $response_status = '';
        $cache_key = md5(json_encode($options));
        
        if ($request_cache && Cache::exists($cache_key)) {
            $response = Cache::recover($cache_key);
            $response_status = 200;
        } else {
            $response = curl_exec($c);
            $response_status = curl_getinfo($c, CURLINFO_HTTP_CODE);
            
            if ($auto_json && is_string($response) && json_decode($response)) {
                // If Request JSON is on, will force return to an object
                $response = json_decode($response);
            }
        }
        
        Benchmark::mark('curl_stop');
        
        // Close connections
        curl_close($c);
        
        $return = $response;
        
        if ($request_cache) {
            // Request cache
            Cache::save($cache_key, $return, false, $request_cache_time);
        }
        
        if (isset($options['profile']) && $options['profile']) {
            // Profiled response
            $return = [
                    'url' => $url,
                    'headers' => fuse($options['headers'], []),
                    'time' => Benchmark::elapsedTime('curl_start', 'curl_stop'),
                    'status' => $response_status,
                    'payload' => $return,
                ];
            
            if ($request_cache) {
                $return['cache'] = true;
            }
        }
        
        return new Response($return);
    }
}
