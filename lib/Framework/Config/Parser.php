<?php

namespace Aurex\Framework\Config;

use Symfony\Component\Yaml\Parser as SymfonyYamlParser,
    Symfony\Component\Yaml\Exception\ParseException;

/**
 * Class Parser
 *
 * @package Aurex\Framework\Config
 */
class Parser
{
    /**
     * @var SymfonyYamlParser
     */
    protected $yamlParser;

    /**
     * @constructor
     *
     * @param SymfonyYamlParser $yamlParser
     */
    public function __construct(SymfonyYamlParser $yamlParser)
    {
        $this->yamlParser = $yamlParser;
    }

    /**
     * @param string $filePath
     *
     * @throws ConfigNotFoundException
     * @throws ParseException
     *
     * @return array The parsed configuration
     */
    public function parseConfig($filePath)
    {
        if (!file_exists($filePath))
        {
            throw new ConfigNotFoundException(sprintf('Configuration file not found: %s', $filePath));
        }

        return $this->yamlParser->parse(file_get_contents($filePath));
    }
}