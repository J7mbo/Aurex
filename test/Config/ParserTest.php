<?php

namespace test\Config;

use Symfony\Component\Yaml\Parser as SymfonyYamlParser,
    Aurex\Framework\Config\Parser;

/**
 * Class ParserTest
 *
 * @package Test\Config
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * Instantiate the parser as a class member
     */
    public function setUp()
    {
        $this->parser = new Parser(new SymfonyYamlParser);
    }

    /**
     * Test that a Yaml configuration file can be successfully parsed
     */
    public function testConfigParsedSuccessfully()
    {
        $filePath = __DIR__ . '/Supporting/test.yml';

        $this->assertArrayHasKey('key', $this->parser->parseConfig($filePath));
    }

    /**
     * Test that a ConfigNotFoundException is thrown when an invalid configuration file path is provided
     */
    public function testConfigFileNotFoundExceptionThrown()
    {
        $this->setExpectedException('Aurex\Framework\Config\ConfigNotFoundException');

        $this->parser->parseConfig('/fileNotFound');
    }
}