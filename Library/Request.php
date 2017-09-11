<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Benchmark;
use \Lollipop\Cache;
use \Lollipop\Config;
use \Lollipop\Log;

/**
 * Request Class 
 *
 * @version     1.3.3
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
     * @vars    array   Centralized session requests
     * 
     */
    static private $_all_requests = array();
    
    /**
     * Check for request(s)
     *
     * @param   mixed   $requests   Request names
     *
     * @return bool
     * 
     */
    static function is($requests) {
        $is = true;
        
        // Also support PUT and DELETE
        parse_str(file_get_contents("php://input"), $_php_request);
        // Merge with POST and GET
        self::$_all_requests = array_merge(self::$_all_requests, array_merge($_REQUEST, $_php_request));
        
        if (is_array($requests)) {
            $returns = array();
            
            foreach ($requests as $request) {
                array_push($returns, isset(self::$_all_requests[$request]));
            }
            
            foreach ($returns as $return) {
                if ($return == false) {
                    $is = false;
                }
            }
        } else {
            $is = isset(self::$_all_requests[$requests]);
        }
        
        return $is;
    }
    
    /**
     * Gets values of request(s)
     *
     * @param   array   $requests   Request names
     *
     * @return  array
     * 
     */
    static function get($requests = null) {
        $var = array();
        
        // Also support PUT and DELETE
        parse_str(file_get_contents("php://input"), $_php_request);
        // Merge with POST and GET
        self::$_all_requests = array_merge(self::$_all_requests, array_merge($_REQUEST, $_php_request));
        
        if (is_array($requests)) {
            foreach ($requests as $request) {
                $var[$request] = isset(self::$_all_requests[$request]) ? self::$_all_requests[$request] : null;
            }
        } else if (is_null($requests)) {
            $var = self::$_all_requests;
        } else {
            $var = (isset(self::$_all_requests[$requests])) ? self::$_all_requests[$requests] : null;
        }
        
        return $var;
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
    static function send(array $options) {
        // Get localdb location in config
        $localdb = Config::get('localdb.folder') ? Config::get('localdb.folder') : LOLLIPOP_STORAGE_LOCALDB;
        
        // Request cache
        $request_cache = !is_null(Config::get('request.cache.enable')) ? Config::get('request.cache.enable') : true;
        $request_cache_time = Config::get('request.cache.time') ? Config::get('request.cache.time') : 1440;
        
        // Auto JSON
        $auto_json = !is_null(Config::get('request.json')) ? Config::get('request.json') : true;
        
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
        curl_setopt($c, CURLOPT_HTTPHEADER, fuse($options['headers'], array()));
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
            curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query(fuse($options['parameters'], array())));
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
            $return = array(
                    'url' => $url,
                    'headers' => fuse($options['headers'], array()),
                    'time' => Benchmark::elapsedTime('curl_start', 'curl_stop'),
                    'status' => $response_status,
                    'payload' => $return,
                );
            
            if ($request_cache) {
                $return['cache'] = true;
            }
        }
        
        return $return;
    }
}

?>
