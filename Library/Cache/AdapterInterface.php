<?php

namespace Lollipop\Cache;

/**
 * Cache Adapter Interface
 * 
 */
interface AdapterInterface
{
    /**
     * Check is cache key exists
     *
     * @param   string  $key    Cache key
     * @return  boolean
     */
    public function exists($key);

    /**
     * Save cache
     *
     * @param   string  $key    Cache key
     * @param   mixed   $data   Data
     * @param   boolean $force  Overwrite existing cache
     * @param   integer $ttl    Time-to-leave (24hrs)
     * @return  void
     */
    public function save($key, $data, $force = false, $ttl = 1440);

    /**
     * Get cache value
     *
     * @param   string  $key
     * @return  mixed
     */
    public function get($key);

    /**
     * Remove cache
     *
     * @param   string  $key
     * @return  boolean
     */
    public function remove($key);

    /**
     * Purge cache
     *
     * @return void
     */
    public function purge();
}
