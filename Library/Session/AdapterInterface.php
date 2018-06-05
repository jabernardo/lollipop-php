<?php

namespace Lollipop\Session;

/**
 * Session Adapter Interface
 * 
 */
interface AdapterInterface
{
    /**
     * Checks if a session variable exists
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @return  bool
     * 
     */
    public function exists($key);
    
    /**
     * Creates a new session or sets an existing sesssion
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @param   string  $value  Session variable value
     * @return  string  Session encrypted key
     * 
     */
    public function set($key, $value);
    
    
    /**
     * Gets session variable's value
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @return  string
     * 
     */
    public function get($key);
    
    /**
     * Get all session variables
     * 
     * @access  public
     * @return  array
     */
    public function getAll();
    
    /**
     * Removes a session variable
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @return  string  Deleted encrypted key
     * 
     */
    public function remove($key);
    
    /**
     * Remove all registered session variables
     * 
     * @return  bool
     * 
     */
    public function removeAll();
}
