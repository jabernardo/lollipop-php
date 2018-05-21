<?php

namespace Lollipop;

defined('LOLLIPOP_BASE') or die('Lollipop wasn\'t loaded correctly.');

/**
 * Benchmark Class
 *
 * @author      John Aldrich Bernardo
 * @email       4ldrich@protonmail.com
 * @package     Lollipop 
 * @description Class for recording benchmarks
 */
class Benchmark
{
    /**
     * @var     array   $_marks     Recorded microtimes
     *
     */
    static private $_marks = [];

    /**
     * Record benchmark
     *
     * @param   string  $mark   Key name
     *
     */
    static public function mark($mark) {
        self::$_marks[$mark] = [
                'time' => microtime(true),
                'memory_usage' => memory_get_peak_usage(true)
            ];
    }
    
    /**
     * Get detailed benchmark
     *
     * @access  public
     * @param   string  $start  Start mark
     * @param   string  $end    End mark
     * @return  array
     *
     */
    static public function elapsed($start, $end) {
        return [
                'time_elapsed' => self::elapsedTime($start, $end),
                'memory_usage_gap' => self::elapsedMemory($start, $end),
                'real_memory_usage' => self::elapsedMemory($start, $end, true)
            ];
    }
    
    /**
     * Get elapsed memory between two marks
     * 
     * @access  public
     * @param   string  $start  Start mark
     * @param   string  $end    End mark
     * @param   bool    $real_usage Get real memory usage
     * @param   bool    $inMB   Show output in MB instead of Bytes
     * @return  mixed   <string> if $inMB is <true>, <longint> if on <false>
     * 
     */
    static public function elapsedMemory($start, $end, $real_usage = false, $inMB = true) {
        $start = isset(self::$_marks[$start]) ? self::$_marks[$start]['memory_usage'] : 0;
        $end = isset(self::$_marks[$end]) ? self::$_marks[$end]['memory_usage'] : 0;
        
        $elapsed = !$real_usage ? ($end - $start) : $end;
        
        return $start ? ($inMB ? (($elapsed / 1024 / 1024) . ' MB') : $elapsed) : null;
    }

    /**
     * Compute the elapsed time of two marks
     *
     * @param   string  $start  Keyname 1
     * @param   string  $end  Keyname 2
     *
     * @return  mixed 
     *
     */
    static public function elapsedTime($start, $end) {
        $start = isset(self::$_marks[$start]) ? self::$_marks[$start]['time'] : 0;
        $end = isset(self::$_marks[$end]) ? self::$_marks[$end]['time'] : 0;

        return $start ? round($end - $start, 10) : null;
    }
}
