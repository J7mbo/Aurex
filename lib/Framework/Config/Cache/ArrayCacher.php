<?php

namespace Aurex\Framework\Config\Cache;

/**
 * Class ArrayCacher
 *
 * Used when no real caching method is available
 *
 * @package Aurex\Framework\Config\Cache
 */
class ArrayCacher implements CacheInterface
{
    /**
     * @var array
     */
    protected $array = [];

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return array_key_exists($key, $this->array) ? $this->array[$key] : false;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->array[$key] = $value;
    }
}