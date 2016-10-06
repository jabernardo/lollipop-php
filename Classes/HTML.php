<?php
    namespace Lollipop;
    
    /**
     * HTML Class
     * 
     * @version     1.0
     * @author      John Aldrich Bernardo
     * @email       4ldrich@protonmail.com
     * @package     Lollipop 
     * @description Class for HTML elements
     */
    class HTML
    {
        /**
         * !DOCTYPE HTML
         * 
         * @access  public
         * @return  void
         * 
         */
        static public function doc() {
            echo '<!DOCTYPE html>';
        }
        
        /**
         * CharSet
         * 
         * @access  public
         * @return  void
         * 
         */
        static public function charset($charset = 'UTF-8') {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=', $charset, '" />';
        }
        
        /**
         * CSS
         * 
         * @access  public
         * @param   mixed   $p2css  Path to CSS
         * @return  void
         * 
         */
        static public function css($p2css) {
            if (is_array($p2css)) {
                foreach ($p2css as $css) {
                    echo '<link rel="stylesheet" type="text/css" href="', $css, '" />';
                }
            } else {
                echo '<link rel="stylesheet" type="text/css" href="', $p2css, '" />';
            }
        }
        
        /**
         * favicon
         * 
         * @access  public
         * @param   string  $ico    Icon file
         * @param   string  $type   Image type
         * @param   string  $rel    Rel type
         * @return  void
         * 
         */
        static public function favicon($ico, $type = 'image/png', $rel = 'icon') {
            echo '<link href="', $ico, '" type="', $type, '" rel="', $rel, '" />';
        }
        
        /**
         * Meta tags
         * 
         * @access  public
         * @param   array   $attr   Attributes
         * @return  void
         * 
         */
        static public function meta(array $attr) {
            echo '<meta ';
            
            foreach ($attr as $key => $value) {
                echo $key, '="', $value, '" ';
            }
            
            echo ' />';
        }
        
        /**
         * Link tags
         * 
         * @access  public
         * @param   array   $attr   Attributes
         * @return  void
         * 
         */
        static public function link(array $attr) {
            echo '<link ';
            
            foreach ($attr as $key => $value) {
                echo $key, '="', $value, '" ';
            }
            
            echo ' />';
        }
        
        /**
         * image
         * 
         * @access  public
         * @param   string  $src    Image source
         * @param   array   $attr   Attributes
         * @return  void
         * 
         */
        static public function image($src, array $attr = array()) {
            echo '<img src="', $src, '" ';
            
            foreach ($attr as $key => $value) {
                echo $key, '="', $value, '" ';
            }
            
            echo ' />';
        }
        
        /**
         * Anchor
         * 
         * @access  public
         * @param   string  $href   Reference link
         * @param   string  $alias  Alias
         * @param   array   $attr   Attributes
         * @return  void
         * 
         */
        static public function anchor($href, $alias, array $attr = array()) {
            echo '<a href="', $href, '" ';
            
            foreach ($attr as $key => $value) {
                echo $key, '="', $value, '" ';
            }
            
            echo '>', $alias, '</a>';
        }
        
        /**
         * Script tag
         * 
         * @access  public
         * @param   mixed   $src    Script source
         * @param   string  $type   Script type
         * @return  void
         * 
         */
        static public function script($src, $type = 'text/javascript') {
            if (is_array($src)) { 
                foreach ($src as $s) {
                    echo '<script src="', $s, '" type="', $type, '"></script>';
                }
            } else {
                echo '<script src="', $src, '" type="', $type, '"></script>';
            }
        }
        
        /**
         * List element
         * 
         * @access  public
         * @param   string  $tag    ul or ol
         * @param   array   $attr   Attributes
         * @return  void
         * 
         */
        static public function nestedList(array $data, $tag = 'ul', array $attr = array()) {
            echo '<', $tag, ' ';
            
            foreach ($attr as $key => $value) {
                echo $key, '="', $value, '" ';
            }
            
            echo '>';
            
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    echo '<li>', $key, '</li>';
                    self::nestedList($val, $tag, $attr);
                } else {
                    echo '<li>', $val[0], '</li>';
                }
            }
            
            echo '</', $tag, '>';
        }
        
        /**
         * Table element
         * 
         * @access  public
         * @param   array   $data   Data
         * @param   array   $attr   Attributes
         * @param   bool    $firstRowHeader Set first row as table header
         * @param   bool    $lastRowHeader  Set last row as table footer
         * @return  void
         * 
         */
        static public function table(array $data, array $attr = array(), $firstRowHeader = false, $lastRowFooter = false) {
            echo '<table ';
            
            foreach ($attr as $key => $value) {
                echo $key, '="', $value, '" ';
            }
            
            echo '>';
            
            // Head
            if ($firstRowHeader) {
                echo '<thead>';
                
                $tr = array();
                
                if (count($data)) {
                    $tr = $data[0];
                    array_shift($data);
                    
                    foreach ($tr as $td) {
                        echo '<td>', $td, '</td>';
                    }
                }
                
                echo '</thead>';
            }
            
            // Foot
            if ($lastRowFooter) {
                echo '<tfoot>';
                
                $tr = array();
                
                if (count($data)) {
                    $tr = $data[count($data) - 1];
                    array_pop($data);
                    
                    foreach ($tr as $td) {
                        echo '<td>', $td, '</td>';
                    }
                }
                
                echo '</tfoot>';
            }
            
            // Body
            echo '<tbody>';
            
            foreach ($data as $tr) {
                echo '<tr>';
                
                foreach ($tr as $td) {
                    echo '<td>', $td, '</td>';
                }
                
                echo '</tr>';
            }
            
            echo '</tbody>';
            
            echo '</table>';
        }
        
        /**
         * p element
         * 
         * @access  public
         * @param   string  $str    String
         * @param   array   $attr   Attributes
         * @return  void
         * 
         */
        static public function p($str, array $attr = array()) {
            echo '<p ';
           
            foreach ($attr as $key => $value) {
                echo $key, '="', $value, '" ';
            }
           
           echo '>', $str, '</p>';
        }
        
        /**
         * Label element
         * 
         * @access  public
         * @param   string  $str    String
         * @param   array   $attr   Attributes
         * @return  void
         * 
         */
        static public function label($str, array $attr = array()) {
            echo '<label ';
           
            foreach ($attr as $key => $value) {
                echo $key, '="', $value, '" ';
            }
           
            echo '>', $str, '</label>';
        }
        
        /**
         * Create an element
         * 
         * @access  public
         * @param   string  $name   Element name
         * @param   array   $attr   Attributes
         * @param   bool    $closing    Has closing tag
         * @return  null
         * 
         */
        static public function elem($name, array $attr = array(), $closing = false) {
            echo '<', $name, ' ';
            
            foreach ($attr as $key => $value) {
                echo $key, '="', $value, '" ';
            }
            
            echo $closing ? '</' . $name . '>' : ' />';
        }
    }
    
?>
