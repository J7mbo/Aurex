<?php

namespace Test\Environment;

use Aurex\Framework\Environment\EnvironmentFactory,
    Aurex\Framework\Aurex,
    Auryn\Injector;

/**
 * Class EnvironmentFactoryTest
 *
 * @package Test\Environment
 */
class EnvironmentFactoryTest extends \PHPUnit_Framework_TestCase
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
     * Tests that the default dev environment within the framework is found
     */
    public function testDefaultEnvironmentsRead()
    {
        $environmentName = 'dev';

        $this->factory->create($environmentName, null);
    }

    /**
     * Tests that an exception is thrown when an invalid environment is given
     */
    public function testInvalidEnvironmentThrowsException()
    {
        $environmentName = 'nonExistent';

        $this->expectEnvironmentNotFoundException();

        $this->factory->create($environmentName);
    }

    /**
     * Tests that an exception is thrown when an environment does not implement the right interface
     */
    public function testWrongInterfaceThrowsException()
    {
        require_once __DIR__ . '/Supporting/InterfaceEnvironment.php';

        $this->expectEnvironmentNotFoundException();

        $this->factory->create('interface', __DIR__ . '/Supporting');
    }

    /**
     * Tests that providing a nonexistent directory throws an exception
     */
    public function testInvalidDirectoryThrowsException()
    {
        $this->expectEnvironmentNotFoundException();

        $this->factory->create('doesNotMatter', '/fakeDirectoryFail');
    }

    /**
     * Tests that creating an environment with a name that does not match the class throws an exception
     */
    public function testIncorrectEnvironmentNameThrowsException()
    {
        require_once __DIR__ . '/Supporting/NameEnvironment.php';

        $this->expectEnvironmentNotFoundException();

        $this->factory->create('name', __DIR__ . '/Supporting');
    }

    /**
     * Unnecessarily aiming for 100% code coverage here
     */
    public function testEnvironmentGetterWorksInAurex()
    {
        $aurex = new Aurex($env = $this->factory->create('dev'), new Injector, []);

        $this->assertSame($env, $aurex->getEnvironment());
    }

    /**
     * Unnecessarily aiming for 100% code coverage here
     */
    public function testNullParamForConfigurationGetsValue()
    {
        (new Aurex($env = $this->factory->create('dev'), new Injector, []))->getConfiguration('dev');
    }

    /**
     * Helper method to set the expected exception to EnvironmentNotFoundException
     */
    protected function expectEnvironmentNotFoundException()
    {
        $namespace = substr(get_class($this->factory), 0, strrpos(get_class($this->factory), '\\'));

        $this->setExpectedException($namespace . '\EnvironmentNotFoundException');
    }
}