<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

/**
 * HTML Tags Class
 * 
 * @version     2.0.1
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class for HTML elements
 * @changes
 *      1.0     HTML Class initial
 *      2.0     Renamed HTML to Tag
 * 
 */
class Tag
{
    /**
     * @var string  Tag name
     * 
     */
    private $_tag = '';
    
    /**
     * @var bool    Is an empty tag?
     * 
     */
    private $_is_empty = false;
    
    /**
     * @var array   Attributes to tag
     * 
     */
    private $_attrs = array();
    
    /**
     * @var string  Tag contents
     * 
     */
    private $_content = '';
    

    /**
     * Create tag
     * 
     * @access  public
     * @param   string  $name   Name of tag
     * @param   bool    $is_empty   Is an empty tag?
     * @return  object  Tag instance
     * 
     */
    public static function create($name, $is_empty = false) {
        $ins = new self();
        
        $ins->_tag = $name;
        $ins->_is_empty = $is_empty;
        
        return $ins;
    }
    
    /**
     * Add attribute to tag
     * 
     * @access  public
     * @param   string  $key    Attribute
     * @param   string  $val    Attribute value
     * @return  object  Tag instance
     * 
     */
    public function add($key, $val) {
        if (!isset($this->_attrs[$key])) {
            $this->_attrs[$key] = array();
        }
        
        if (strtolower($key) == 'id') {
            // value for `id` should be one only
            $this->_attrs[$key] = $val;
        } else {
            // else append it
            array_push($this->_attrs[$key], $val);
        }
        
        return $this;
    }
    
    /**
     * Remove tag attribute
     * 
     * @access  public
     * @param   string  $key
     * @return  object  Tag instance
     * 
     */
    public function remove($key) {
        if (isset($this->_attrs[$key])) {
            unset($this->_attrs[$key]);
        }
        
        return $this;
    }
    
    /**
     * Set contents of tag
     * 
     * @access  public
     * @param   string  $data   Contents
     * @param   bool    $append Append or set tag content. (default true)
     * @return  object  Tag instance
     * 
     */
    public function contains($data, $append = true) {
        $this->_content = $append ? $this->_content . $data : $data;
        
        return $this;
    }
    
    /**
     * PHP magic __toString function
     * 
     * @access  public
     * @description Convert this tag instance to string
     * @return  string  HTML Tag
     * 
     */
    public function __toString() {
        $elem = '<' . $this->_tag;
        
        foreach ($this->_attrs as $k => $v) {
            $elem .= ' ' . $k . '="';
            
            if (is_array($v)) {
                $elem .= implode(' ', $v);
            } else {
                $elem .= $v;
            }
            
            $elem .= '"';
        }
        
        if ($this->_is_empty) {
            // Close tag if an empty tag
            $elem .= '/>';
        } else {
            // else...
            $elem .= '>' . $this->_content . '</' . $this->_tag . '>';
        }
        
        return $elem;
    }
}

?>
