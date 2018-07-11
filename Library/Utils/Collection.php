<?php

namespace Lollipop\Utils;

/**
 * Array Collection Trait
 * 
 * @package lollipop
 * @author  John Aldrich Bernardo
 * 
 */
trait Collection
{
    /**
     * Array merge including integers as key
     * 
     * @access  public
     * @param   array ...$args  Arguments
     * @return  array
     * 
     */
    static function arrayMerge() {
        $output = [];
        
        foreach(func_get_args() as $array) {
            foreach($array as $key => $value) {
                $output[$key] = isset($output[$key]) ?
                    array_merge($output[$key], $value) : $value;
            }
        }
        
        return $output;
    }
    
    /**
     * Array get using dots to traverse keys
     * 
     * @example
     * 
     *  $arr = ['test' => ['word' => 'hello']];
     *  print_r(arrayGet($arr, 'test.word'));
     * 
     * @access  public
     * @param   array   $var    Local declared variable
     * @param   string  $key    Key
     * @param   mixed   $default    Default value if $key isn't existing
     * @return  mixed
     * 
     */
    static function arrayGet(&$var, $key, $default = null) {
        $toks = explode('.', $key);
        
        for ($i = 0; $i < count($toks); $i++) {
            $var = &$var[$toks[$i]];
        }
        
        return is_array($var) || is_object($var)
                ? json_decode(json_encode($var))
                : (is_null($var) && !is_null($default) ? $default : $var);
    }
    
    /**
     * Set array key value using dots to traverse keys
     * 
     * @example
     * 
     *  $arr = [];
     *  arraySet($arr, 'sample.word', 'Hello');
     * 
     * @access  public
     * @param   array   $var    Local declared variable
     * @param   string  $key    Array key
     * @param   mixed   $val    Value
     * @return  void
     * 
     */
    static function arraySet(&$var, $key, $val) {
        $toks = explode('.', $key);
        
        for ($i = 0; $i < count($toks); $i++) {
            $var = &$var[$toks[$i]];
        }
        
        $var = $val;
    }
    
    /**
     * Remove array key using dots to traverse keys
     * 
     *  $arr = ['test' => ['word' => 'hello']];
     *  arrayUnset($arr, 'sample.word');
     * 
     * @access  public
     * @param   array   $var    Local declared variable
     * @param   string  $key    Array key
     * @param   mixed   $val    Value
     * @return  void
     * 
     */
    static function arrayUnset(&$var, $key) {
        $toks = explode('.', $key);
        $toks_len = count($toks);
        $last = null;
        
        for ($i = 0; $i < $toks_len - 1; $i++) {
            $var = &$var[$toks[$i]];
        }

        if (isset($toks[$toks_len - 1])) {
            $last = $toks[$toks_len - 1];
        }

        if ($last) unset($var[$last]);
    }
}
