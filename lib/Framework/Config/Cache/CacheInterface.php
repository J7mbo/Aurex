<?php

namespace Aurex\Framework\Config\Cache;

/**
 * Interface CacheInterface
 *
 * Represents an object capable of providing caching for configuration data
 *
 * @package Aurex\Framework\Config\Cache
 */
interface CacheInterface
{
    /**
     * @param string $key
     *
     * @return mixed|false Data if found, false if not exists
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function set($key, $value);
}