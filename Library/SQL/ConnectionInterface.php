<?php

namespace Lollipop\SQL;

/**
 * Connection Adapter Interface
 * 
 */
interface ConnectionInterface
{
    /**
     * Execute query
     * 
     * @param   bool    $cache  Enable cache (for queries)
     * @return  mixed
     * 
     */
    public function execute($cache = true);
}
