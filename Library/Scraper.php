<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Log;

/**
 * Web Scraper Class
 * 
 * @version     1.1.1
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class for scrapping websites
 *
 */
class Scraper
{
    /**
     * @var    string    $_current_url    Current URL in used
     *
     */
    private $_current_url;

    /**
     * @var    string    $_page_contents    Page contents of current URL
     *
     */
    private $_page_contents;

    /**
     * @var    object    $_dom_document    Instance of DOMDocument
     *
     */
    private $_dom_document;

    /**
     * @var    object    $_dom_x_path    Instance of DOMXPath
     *
     */
    private $_dom_x_path;

    /**
     * Web Scrapper
     *
     * @param    string    $url    URI of webpage to scrap
     *
     */
    function __construct($url, $post = null, $user_agent = null) {
        // Check first if given string is a valid URL 
        if (!filter_var($uri, FILTER_VALIDATE_URL)) {
            Log::error('URL is invalid', true);
        }
        
        // Set current url
        $this->_current_url = $url;
        
        // Initialize a new DOMDocument
        $this->_dom_document = new \DOMDocument();
        
        // Get cURL resource
        $curl = curl_init();
        
        // Return response and set url
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        
        // If POST parameters are applied
        if (is_array($post)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }
        
        // If user-agent is required
        if (is_string($user_agent)) {
            curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
        }
        
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        
        if (!$resp) {
            Log::error($url . ' not Found!');
        }
        
        // Close request to clear up some resources
        curl_close($curl);
        
        // Load HTML file from response
        @$this->_dom_document->loadHTML($resp);
        
        // Set some options
        $this->_dom_document->recover = true;
        $this->_dom_document->strictErrorChecking = false;
        
        // Initialize a new DOMXPath from DOMDocument
        $this->_dom_x_path = new \DOMXPath($this->_dom_document);
    }

    /**
     * Get contents of an element by using attributes
     *
     * @example    getContentsByAttr('class', 'thumbnail');
     *
     * @param    string    $attr        Element attribute
     * @param    string    $attr_value    Value of element attribute
     *
     * @return array
     *
     */
    public function getContentsByAttr($attr, $attr_value) {
        $nodes = $this->_dom_x_path->query("//*[contains(@{$attr}, '{$attr_value}')]");
        
        $contents = array();
        
        foreach ($nodes as $node) {
            array_push($contents, $node->nodeValue);
        }
        
        return $contents;
    }
    
    /**
     * Get contents of an element by using element name
     *
     * @example    getContentsByElem('div');
     *
     * @param    string    $elem        Element name
     *
     * @return array
     *
     */
    public function getContentsByElem($elem) {
        $nodes = $this->_dom_x_path->query("//$elem");
        
        $contents = array();
        
        foreach ($nodes as $node) {
            array_push($contents, $node->nodeValue);
        }
        
        return $contents;
    }
    
    /**
     * Get attribute value using other attributes
     *
     * @example     getAttrByAttr('class', 'thumbnail', 'href')
     * 
     * @param    string    @attr            Element attribute
     * @param    string    @attr_value        Element attribute value
     * @param    string    @attr_to_get    Name of attribute to get    
     *
     * @return    array
     *
     */
    public function getAttrByAttr($attr, $attr_value, $attr_to_get) {
        $nodes = $this->_dom_x_path->query("//*[contains(@{$attr}, '{$attr_value}')]");
        
        $contents = array();
        
        foreach ($nodes as $node) {
            array_push($contents, $node->getAttribute($attr_to_get));
        }
        
        return $contents;
    }

    /**
     * Get attributes of elements
     *
     * @example
     *         getAttrByElemWithAttr('a');
     *
     * Output:
     *        Array
     *        (
     *            [0] => Array
     *            (
     *                [href] => www.sample.com
     *                [class] => thumbnail
     *            )
     *        )
     * 
     *
     *
     * @param    string    $element    Element (e.g. a, div, img)
     *
     * @return     array
     *
     */
    public function getAttrByElem($element) {
        $nodes = $this->_dom_x_path->query("//{$element}");
        
        $contents = array();
        $attrs = array();
        
        foreach ($nodes as $node) {
            
            $attrs = array();
            
            foreach ($node->attributes as $attr) {
                $attrs["{$attr->nodeName}"] = $attr->nodeValue;
            }
            
            array_push($contents, $attrs);
            
        }
        
        return $contents;
    }
    
    /**
     * Get attributes by element using another attributes
     *
     * @example:
     *         getAttrByElemWithAttr('a', 'class', 'thumbnail');
     *
     * Output:
     *        Array
     *        (
     *            [0] => Array
     *            (
     *                [href] => www.sample.com
     *                [class] => thumbnail
     *            )
     *        )
     * 
     *
     *
     * @param    string    $element    Element (e.g. a, div, img)
     * @param    string    $attr        Attribute name (e.g. class, id)
     * @param    string    $attr_value    Attribute value (e.g. thumbnail)
     *
     * @return     array
     *
     */
    public function getAttrByElemWithAttr($element, $attr, $attr_value) {
        $nodes = $this->_dom_x_path->query("//{$element}[contains(@{$attr}, '{$attr_value}')]");
        
        $contents = array();
        $attrs = array();
        
        foreach ($nodes as $node) {
            
            $attrs = array();
            
            foreach ($node->attributes as $attr) {
                $attrs["{$attr->nodeName}"] = $attr->nodeValue;
            }
            
            array_push($contents, $attrs);
            
        }
        
        return $contents;
    }
}

?>
