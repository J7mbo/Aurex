<?php

namespace Test\Environment;

use Aurex\Framework\Environment\EnvironmentFactory,
    Aurex\Framework\Environment\DevEnvironment,
    Auryn\Provider as Injector,
    Aurex\Framework\Aurex;

/**
 * Class DevEnvironmentTest
 *
 * @package Test\Environment
 */
class DevEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnvironmentFactory
     */
    protected $factory;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->factory = new EnvironmentFactory;
    }

    /**
     * Tests that the default Dev environment can be created
     */
    public function testDefaultDevEnvironmentCanBeCreated()
    {
        $this->factory->create('dev');
    }

    /**
     * Tests that the default dev environment updates xdebug settings
     */
    public function testDefaultDevEnvironmentSetsDebugToTrue()
    {
        $env = $this->factory->create('dev');

        $env->perform($aurex = new Aurex(new DevEnvironment, new Injector, []));

        $this->assertTrue($aurex['debug']);
    }
}