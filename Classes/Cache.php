<?php
    namespace Lollipop;

    /**
     * Simple page Caching
     *
     * @version     3.0.3
     * @author      John Aldrich Bernardo
     * @email       4ldrich@protonmail.com
     * @package     Lollipop 
     * 
     */
    class Cache
    {
        /**
         * Checks cache folder
         *
         * @access  private
         * @return  void
         *
         */
        static private function _checkFolder() {
            if (!is_dir(LOLLIPOP_CACHE)) {
                throw new \Exception('Can\'t find app/cache folder');
            }
            
            if (!is_writable(LOLLIPOP_CACHE)) {
                throw new \Exception('Permission denied for app/cache');
            }
        }
        
        /**
         * is_serialized from WordPress
         *
         * @param   string  $data   Serialized data
         * @param   bool    $strict
         * @return  bool
         *
         */
        static private function _isSerialized($data, $strict = true) {
            // if it isn't a string, it isn't serialized.
            if (!is_string($data)) {
                return false;
            }
            
            $data = trim($data);
            
            if ('N;' == $data) {
                return true;
            }
            
            if (strlen($data) < 4) {
                return false;
            }
            
            if (':' !== $data[1]) {
                return false;
            }
            
            if ($strict) {
                $lastc = substr($data, -1);
                
                if (';' !== $lastc && '}' !== $lastc) {
                    return false;
                }
            } else {
                $semicolon = strpos($data, ';');
                $brace     = strpos($data, '}');
                // Either ; or } must exist.
                if (false === $semicolon && false === $brace)
                    return false;
                // But neither must be in the first X characters.
                if (false !== $semicolon && $semicolon < 3)
                    return false;
                if (false !== $brace && $brace < 4)
                    return false;
            }
            
            $token = $data[0];
            
            switch ($token) {
                case 's':
                    if ($strict) {
                        if ('"' !== substr($data, -2, 1)) {
                            return false;
                        }
                    } elseif (false === strpos($data, '"')) {
                        return false;
                    }
                    // or else fall through
                case 'a':
                case 'O':
                    return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
                case 'b':
                case 'i':
                case 'd':
                    $end = $strict ? '$' : '';
                    return (bool)preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
            }
            
            return false;
        }
        
        /**
         * Cache janitor
         *
         * @access  private
         * @param   string  $key    Cache key
         * @return  void
         * 
         */
        static private function _janitor($key) {
            $fn = LOLLIPOP_CACHE . sha1($key);
            
            if (file_exists($fn)) {
                $contents = file_get_contents($fn);
                
                if (!self::_isSerialized($contents)) {
                    unlink($fn);
                    return;
                }
    
                $data = unserialize($contents);
    
                if (isset($data['date_created']) && isset($data['ttl']) && isset($data['data'])) {
                    if (time() - (int)$data['date_created'] >= $data['ttl']) {
                        unlink($fn);
                    }
                } else {
                    unlink($fn);
                }
            }
        }
        
        /**
         * Check if cache exists
         *
         * @access  public
         * @param   string  $key    Cache key
         * @return  bool
         *
         */
        static public function exists($key) {
            self::_checkFolder();
            self::_janitor($key);
            
            return file_exists(LOLLIPOP_CACHE . sha1($key));
        }
        
        /**
         * Save cache
         *
         * @access  public
         * @param   string  $key    Cache key
         * @param   mixed   $data   Data to be saved
         * @param   bool    $force  Force to override old data
         * @param   int     $ttl    Time-to-leave (default to 24 Hrs)
         * @return  void
         *
         */
        static public function save($key, $data, $force = false, $ttl = 1440) {
            self::_checkFolder();
            
            $ttl = $ttl * 60; // Minutes to Seconds
            
            if (!self::exists($key) || $force) {
                $data = array(
                    'date_created' => time(),
                    'ttl' => $ttl,
                    'data' => $data
                );
                
                file_put_contents(LOLLIPOP_CACHE . sha1($key), serialize($data));
            }
        }
        
        /**
         * Recover cache
         * 
         * @access  public
         * @param   string  $key    Cache key
         * @return  mixed
         *
         */
        static public function recover($key) {
            self::_checkFolder();
            
            if (self::exists($key)) {
                $data = unserialize(file_get_contents(LOLLIPOP_CACHE . sha1($key)));
                
                return isset($data['data']) ? $data['data'] : '';
            }
            
            return '';
        }
        
        /**
         * Remove cache
         *
         * @access  public
         * @param   string  $key
         * @return  bool
         *
         */
        static public function remove($key) {
            self::_checkFolder();
            
            $cache = LOLLIPOP_CACHE . sha1($key);
            
            if (file_exists($cache)) {
                unlink($cache);
            }
            
            return false;
        }
        
        /**
         * Remove all cache
         *
         * @access  public
         * @return  void
         *
         */
        static public function purge() {
            self::_checkFolder();
            
            // Get all files from the cache folder
            $contents = glob(LOLLIPOP_CACHE . '*');
    
            // Remove cache files
            foreach ($contents as $content) {
                if (is_file($content)) {
                    unlink($content);
                }
            }
        }
    }

?>
