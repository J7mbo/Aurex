<?php

namespace Aurex\Framework\Config\Cache;

/**
 * Class MemcachedCacher
 *
 * Provides memcached data saving, if available
 *
 * @package Aurex\Framework\Config\Cache
 */
class MemcachedCacher implements CacheInterface
{
    /**
     * @var string The name of the memcache extension to check for
     */
    const EXTENSION_NAME = 'memcached';

    /**
     * @var \Memcache
     */
    protected $memcached;

    /**
     * @var boolean
     */
    protected $extensionLoaded;

    /**
     * @constructor
     *
     * @param \Memcached $memcached
     */
    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
        $this->extensionLoaded = extension_loaded(self::EXTENSION_NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return !$this->extensionLoaded || !($config = $this->memcached->get($key)) ? false : $config;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        if ($this->extensionLoaded)
        {
            $this->memcached->set($key, $value);
        }
    }
}