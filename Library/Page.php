<?php
    namespace Lollipop;
    
    /**
     * Page Class 
     *
     * @version     1.0
     * @author      John Aldrich Bernardo
     * @email       4ldrich@protonmail.com
     * @package     Lollipop 
     * @description Class containing usable functions for HTML
     * 
     */
    class Page
    {
        /**
         * Reloads current page
         * 
         */
        static function reload() {
            header('location: ' . $_SERVER['REQUEST_URI']);
        }
        
        /**
         * Redirect page to another urldecode
         *
         * @paramstring     $uri    Web address 
         */
        static function redirect($uri) {
            // Check first if given string is a valid URL 
            if (!filter_var($uri, FILTER_VALIDATE_URL)) {
                \Lollipop\Log::error('URL is invalid', true);
            }
            
            header('location: ' . $uri);
        }
        
        /**
         * Alias read file
         * 
         * @param   string  $view   File address
         * @param   array   $data   Data
         *
         * @return  string
         */
        static function render($view, array $data = array()) {
            if (file_exists($view)) {
                if (is_array($data)) {
                    foreach ($data as $_data => $_value) {
                        $$_data = $_value;
                    }
                } else {
                    \Lollipop\Log::error('Lollipop Exception: Can\'t define variable');
                }
            
                $file = new \SplFileInfo($view);
                $file_ext = strtolower($file->getExtension());
                
                
                ob_start();
                
                if ($file_ext == 'php' || $file_ext == 'phtml') {
                    include($view);
                } else {
                    readfile($view);
                }
                
                $output = ob_get_clean();
                    
                echo $output;
                
                return true;
            }
            
            return false;
        }
    }
?>
