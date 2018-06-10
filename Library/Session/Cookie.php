<?php

namespace Lollipop\Session;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\Text;
use \Lollipop\HTTP\Cookie as HTTPCookie;

/**
 * Secured Session Class using PHP Build-it Session 
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * 
 */
class Cookie implements \Lollipop\Session\AdapterInterface
{
    /**
     * Session name
     * 
     * @access  private
     * @var     string
     * 
     */
    private $name       = 'session';
    
    /**
     * Cookie path
     * 
     * @access  private
     * @var     string
     * 
     */
    private $path       = '';
    
    /**
     * Cookie domain
     * 
     * @access  private
     * @var     string
     * 
     */
    private $domain     = '';
    
    /**
     * Storage
     * 
     * @access  private
     * @var     object
     * 
     */
    private $data;
    
    /**
     * Class construct
     * 
     * @access  public
     * 
     */
    function __construct() {
        $this->name     = sha1(spare_nan(Config::get('session.name'), 'session'));
        $this->path     = spare_nan(Config::get('session.path'), '');
        $this->domain   = spare_nan(Config::get('session.domain'), '');
        
        $this->data = $this->restore();
        
        if (!isset($this->data->sugar)) {
            $this->data->sugar = fuse($this->data->sugar, sha1(Text::random(10)));
            
            $this->store($this->data);
        }
    }
    
    /**
     * Store session into cookie
     * 
     * @access  private
     * @return  void
     * 
     */
    private function store($data) {
        $enc_data = Text::lock(json_encode($data));
        HTTPCookie::set($this->name, $enc_data, 0, $this->path, $this->domain);
    }

    /**
     * Restore session from cookie
     * 
     * @access  private
     * @return  object
     * 
     */
    private function restore() {
        if (!HTTPCookie::exists($this->name)) return (object)[];
        
        return json_decode(Text::unlock(HTTPCookie::get($this->name)));
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
        return isset($this->data->$key);
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
        $this->data->$key = $value;
        $this->store($this->data);
        
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
        return fuse($this->data->$key, '');
    }
    
    /**
     * Get session id
     * 
     * @access  public
     * @return  string
     * 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get all session variables
     * 
     * @access  public
     * @return  array
     */
    public function getAll() {
        return $this->data;
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
        if (isset($this->data->$key)) unset($this->data->$key);
        
        $this->store($this->data);
        
        return ;
    }
    
    /**
     * Remove all registered session variables
     * 
     * @return  bool
     * 
     */
    public function removeAll() {
        $this->store((object)[]);
    }
}
