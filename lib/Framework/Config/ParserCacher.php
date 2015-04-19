<?php

namespace Aurex\Framework\Config;

use Symfony\Component\Yaml\Exception\ParseException,
    Aurex\Framework\Config\Cache\CacheInterface;

/**
 * Class ParserCacher
 *
 * If wrapped around a Parser, provides caching for the given configuration file
 *
 * @package Aurex\Framework\Config
 */
class ParserCacher
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var CacheInterface
     */
    protected $cacher;

    /**
     * @constructor
     *
     * @param Parser         $parser
     * @param CacheInterface $cacher
     */
    public function __construct(Parser $parser, CacheInterface $cacher)
    {
        $this->parser = $parser;
        $this->cacher = $cacher;
    }

    /**
     * @param string $filePath
     *
     * @throws ConfigNotFoundException
     * @throws ParseException
     *
     * @return array The parsed configuration
     *
     * @see Parser::parseConfig()
     *
     * @note Configuration data stored in cache with the key as the file path
     */
    public function parseConfig($filePath)
    {
        $data = $this->cacher->get($filePath);

        if (!$data)
        {
            $data = $this->parser->parseConfig($filePath);

            $this->cacher->set($filePath, $data);
        }

        return $data;
    }
}