<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

use \Lollipop\Config;
use \Lollipop\Text;

/**
 * Page Class 
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class containing usable functions for HTML
 * 
 */
class Page
{
    /**
     * Alias read file
     * 
     * @param   string  $view   File address
     * @param   array   $data   Data
     * @throws  \Lollipop\Exception\Runtime
     * 
     * @return  string
     */
    static function render($view, array $data = []) {
        if (file_exists($view)) {
            if (is_array($data)) {
                foreach ($data as $_data => $_value) {
                    if (Config::get('security.anti_xss') && is_string($_value)) {
                        $_value = Text::entities($_value);
                    }
                    
                    $$_data = $_value;
                }
            } else {
                throw new \Lollipop\Exception\Runtime('Cannot define variable');
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
            
            return $output;
        }
        
        return false;
    }
}
