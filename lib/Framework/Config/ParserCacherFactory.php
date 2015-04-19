<?php

namespace Aurex\Framework\Config;

use Aurex\Framework\Config\Cache\MemcachedCacher,
    Aurex\Framework\Config\Cache\CacheInterface,
    Aurex\Framework\Config\Cache\ArrayCacher;

/**
 * Class ParserCacherFactory
 *
 * Use either memcache or ArrayCacher depending on their availability
 *
 * @package Aurex\Framework\Config
 */
class ParserCacherFactory
{
    /**
     * @var string Default memcached port
     */
    const SERVER = 'localhost';

    /**
     * @var int Default memcached port
     */
    const PORT = 11211;

    /**
     * @return CacheInterface
     */
    public function make()
    {
        if (extension_loaded('memcached'))
        {
            $memcached = new \Memcached;
            $memcached->addServer(self::SERVER, self::PORT);

            return new MemcachedCacher($memcached);
        }
        else
        {
            return new ArrayCacher;
        }
    }
}