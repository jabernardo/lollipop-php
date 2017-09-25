<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

/**
 * Assertion Tests Class
 *
 * @version     1.0.0
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for simple Assertion Testing
 * 
 */
class Assert
{
    /**
     * Simple Equals: ==
     * 
     * @access  public
     * @param   mixed   $obj1   First Object
     * @param   mixed   $obj2   Second Object
     * @return  bool
     * 
     */
    public static function equals($obj1, $obj2) {
        return $obj1 == $obj2;
    }
    
    /**
     * Strict type equals: ===
     * 
     * @access  public
     * @param   mixed   $obj1   First Object
     * @param   mixed   $obj2   Second Object
     * @return  bool
     * 
     */
    public static function strictEquals($obj1, $obj2) {
        return $obj1 === $obj2;
    }
    
    /**
     * Simple Not Equals: !=
     * 
     * @access  public
     * @param   mixed   $obj1   First Object
     * @param   mixed   $obj2   Second Object
     * @return  bool
     * 
     */
    public static function notEquals($obj1, $obj2) {
        return $obj1 != $obj2;
    }
    
    /**
     * Simple Not Equals: !==
     * 
     * @access  public
     * @param   mixed   $obj1   First Object
     * @param   mixed   $obj2   Second Object
     * @return  bool
     * 
     */
    public static function strictNotEquals($obj1, $obj2) {
        return $obj1 !== $obj2;
    }
    
    /**
     * Assert true
     * 
     * @access  public
     * @param   bool    $obj    Object
     * @return  bool
     * 
     */
    public static function true($obj) {
        return true === $obj;
    }
    
    /**
     * Assert false
     * 
     * @access  public
     * @param   bool    $obj    Object
     * @return  bool
     * 
     */
    public static function false($obj) {
        return false === $obj;
    }
    
    /**
     * Expect exception
     * 
     * @access  public
     * @param   callable    $callback   Callback
     * @return  bool
     * 
     */
    public static function exception($callback) {
        if (is_callable($callback)) {
            try {
                $callback();
            } catch (\Exception $e) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Expect no-exception thrown
     * 
     * @access  public
     * @param   callable    $callback   Callback
     * @return  bool
     * 
     */
    public static function noException($callback) {
        if (is_callable($callback)) {
            try {
                $callback();
            } catch (\Exception $e) {
                return false;
            }
        }
        
        return true;
    }
}

?>
