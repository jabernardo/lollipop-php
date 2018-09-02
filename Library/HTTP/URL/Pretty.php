<?php

namespace Lollipop\HTTP\URL;

/**
 * Pretty URL Parser Class
 * 
 * @author  John Aldrich Bernardo <4ldrich@protonmail.com>
 * @link    https://github.com/jabernardo/calf/blob/master/src/Calf/HTTP/RouteParser.php
 * 
 */
class Pretty
{
    /**
     * @var string  Active URL Path
     * 
     */
    private $_path = '';
    
    /**
     * @var string  Route pattern
     * 
     */
    private $_pattern = '';
    
    /**
     * @var array   Mapped Keys Value of matches
     * 
     */
    private $_mapped = [];
    
    /**
     * Class construct
     * 
     * @example
     * 
     * Route:   /page/{number}
     * URL:     /page/1
     * 
     * Matches:
     * 
     * [
     *      "number" => 1
     * ]
     * 
     * @access  public
     * @param   string  $path   Active URL Path
     * @return  void
     * 
     */
    function __construct($path) {
        $this->_path = trim($path, '/');
    }
    
    /**
     * Check if Route matches Path
     * 
     * @access  public
     * @param   string  $route  Route pattern
     * @return  bool
     * 
     */
    public function test($route) {
        // Make sure that matches are empty
        $this->_mapped = [];
        
        // Save pattern for future use
        $this->_pattern = $route;

        // Find all route keys without RegEx pattern
        //
        // Example:
        //      /get/product/{name}
        // To:
        //      /get/product/{name:([^\/]+)}
        //
        // Optional parameters (e.g)
        //
        //  Options and required
        //      /pages[/{page:\d+}]/category{category}
        //  Options on middle
        //      api/test/get[{asd:\d+}]/fruits
        //  Single options
        //      /pages[/{page:\d+}]
        //  Multiple Optionals
        //      /pages[/{page:\d+}]/category[/{category}]
        $translation = preg_replace(['/\//is', '/\[([^\[]+)\]/is', '/{(\w*?)}/is'],['\/', '(?:$1)?', '{$1:([^\/]+)}'], $route);
        
        // Keys and RegEx 
        $keyex = [];
        
        // This will retrieve all keys and RegEx to be found
        // on our matching
        preg_match_all('/{(\w*?):(.*?)}/', $translation, $keyex);
        
        // Make sure all RegEx given in the route pattern
        // are grouped
        $groups = array_map(function($val) {
            return '(' . trim(trim($val, ')'), '(') . ')';
        }, $keyex[2]);

        // Translate all keyex found on the route then
        // Replace it with the grouped regex pattern
        $translation = str_replace($keyex[0], $groups, $translation);
        
        // Make a temporary storage for our matches
        $vals = [];

        // Test the current route in iteration with the current path
        // Then store the good finds
        $test = preg_match("#^$translation$#is", $this->_path, $vals);

        // Remove the first key, first key is usually the string that matches our pattern
        array_shift($vals);
        
        // Let's create the new mapping for key-value
        for ($i = 0; $i < count($vals); $i++) {
            if (isset($keyex[1][$i]) && isset($vals[$i])) {
                $this->_mapped[$keyex[1][$i]] = $vals[$i];
            }
        }
        
        // Return test results
        return $test;
    }

    /**
     * Get route URL with parameters passed on.
     * 
     * @example
     *      // Url: /categories/pages/{page}
     *      $pretty->getUrl(['page' => 10]);
     *      // Ouput: /categories/pages/10
     * 
     * @access  public
     * @param   array   $data   URL parameters
     * @return  string  Generated URL
     * 
     */
    public function getUrl(array $data = []) {
        // Guess the what URL should be looked like
        $translation_guess = preg_replace(['/\[([^\[]+)\]/is', '/{(\w*?)}/is', '/{(\w*?):(:?.*?)}/is'], '{$1}', $this->_path);
        
        $keys = [];
        preg_match_all('/{(\w*?)}/is', $translation_guess, $keys);

        foreach ($keys[1] as $key) {
            $translation_guess = str_replace('{'.$key.'}', isset($data[$key]) ? $data[$key] : 'null', $translation_guess);
        }

        $translation_guess;

        // Get raw pattern from route
        $translation = preg_replace(['/\//is', '/\[([^\[]+)\]/is', '/{(\w*?)}/is'],['\/', '(?:$1)?', '{$1:([^\/]+)}'], $this->_path);
        $keyex = [];
        preg_match_all('/{(\w*?):(.*?)}/', $translation, $keyex);
        $groups = array_map(function($val) {
            return '(' . trim(trim($val, ')'), '(') . ')';
        }, $keyex[2]);
        $translation = str_replace($keyex[0], $groups, $translation);

        // Check if guess is valid
        $test = preg_match("#^$translation$#is", $translation_guess, $vals);

        return $test ? $translation_guess : '';
    }
    
    /**
     * Get pattern from last test
     * 
     * @return  string
     * 
     */
    public function getPattern() {
        return $this->_pattern;
    }
    
    /**
     * Get matches
     * 
     * @return  array
     * 
     */
    public function getMatches() {
        return $this->_mapped;
    }
}
