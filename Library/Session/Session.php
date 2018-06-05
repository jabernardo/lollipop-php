<?php

namespace Lollipop\Session;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\Text;

/**
 * Secured Session Class using PHP Build-it Session 
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * 
 */
class Session implements \Lollipop\Session\AdapterInterface
{
    /**
     * Class construct
     * 
     * @access  public
     * 
     */
    function __construct() {
        if (!isset($_SESSION)) {
            session_start();
        }
    }
    
    /**
     * Returns the key used in encrypting session variables
     *
     * @access  private
     * @return  string
     * 
     */
    private function sugar() {
        return md5(Config::get('sugar', Text::lock(SUGAR)));
    }
    
    /**
     * Secure Key
     * 
     * @access  private
     * @return  string
     * 
     */
    private function secureKey($text) {
        return substr(sha1($text), 0, 10);
    }
    
    /**
     * Secure value using sugar
     * 
     * @access  private
     * @return  string
     * 
     */
    private function secureValue($text) {
        return Text::lock($text, $this->sugar());
    }
    
    /**
     * Checks if a session variable exists
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @return  bool
     * 
     */
    public function exists($key) {
        $key = $this->secureKey($key);
        
        if (isset($_SESSION[$key])) return true;
        
        return false;
    }
    
    /**
     * Creates a new session or sets an existing sesssion
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @param   string  $value  Session variable value
     * @return  string  Session encrypted key
     * 
     */
    public function set($key, $value) {
        $key = $this->secureKey($key);
        $_SESSION[$key] = $this->secureValue($value);
        
        return $key;
    }

    /**
     * Gets session variable's value
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @return  string
     * 
     */
    public function get($key) {
        $key = $this->secureKey($key);
        
        if (isset($_SESSION[$key])) {
            return trim(Text::unlock($_SESSION[$key], $this->sugar()));
        } else {
            return '';
        }
    }
    
    /**
     * Get session id
     * 
     * @access  public
     * @return  string
     * 
     */
    public function getId() {
        return session_id();
    }

    /**
     * Get all session variables
     * 
     * @access  public
     * @return  array
     */
    public function getAll() {
        return $_SESSION;
    }

    /**
     * Removes a session variable
     *
     * @access  public
     * @param   string  $key    Session variable name
     * @return  string  Deleted encrypted key
     * 
     */
    public function remove($key) {
        $key = $this->secureKey($key);
        
        if (isset($_SESSION[$key])) unset($_SESSION[$key]);
        
        return $key;
    }
    
    /**
     * Remove all registered session variables
     * 
     * @return  bool
     * 
     */
    public function removeAll() {
        return session_unset();
    }
}
