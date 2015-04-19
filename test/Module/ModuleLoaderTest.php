<?php

namespace Test\Module;

use Aurex\Framework\Environment\DevEnvironment,
    Test\Module\Supporting\TestFailureModule,
    Test\Module\Supporting\TestSuccessModule,
    Aurex\Framework\Module\ModuleLoader,
    Aurex\Framework\Aurex,
    Auryn\Provider;

/**
 * Class ModuleLoaderTest
 *
 * @package Test\Module
 */
class ModuleLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that a custom Module can be loaded successfully
     */
    public function testCanLoadCustomModule()
    {
        $module = new TestSuccessModule;
        $aurex  = $this->makeAurex('test');

        (new ModuleLoader)->load($aurex, get_class($module));

        $this->assertTrue($aurex->moduleIsLoaded($module));
    }

    /**
     * Test that a Module's failed integration does not cause it to be registered as 'loaded' within Aurex
     */
    public function testFailedModuleIntegrationReportsNotLoadedInAurex()
    {
        $module = new TestSuccessModule;
        $aurex  = $this->makeAurex('falseKey');

        try
        {
            (new ModuleLoader)->load($aurex, get_class($module));
        }
        catch (\Exception $e)
        {
            /** Ignore exceptions here **/
        }

        $this->assertFalse($aurex->moduleIsLoaded($module));
    }

    /**
     * Test that a Module that does not exist throws a ModuleNotFoundException
     */
    public function testClassNotExistsThrowsModuleNotFoundException()
    {
        $aurex = $this->makeAurex('');

        $this->setExpectedException('Aurex\Framework\Module\ModuleNotFoundException');

        (new ModuleLoader)->load($aurex, 'fakeModule');
    }

    /**
     * Test that a custom provided Module throws an exception if it does not implement the correct interface
     */
    public function testCustomModuleWithoutInterfaceThrowsModuleNotFoundException()
    {
        $aurex  = $this->makeAurex('test');
        $module = new TestFailureModule;

        $this->setExpectedException('Aurex\Framework\Module\ModuleNotFoundException');

        (new ModuleLoader)->load($aurex, get_class($module));
    }

    /**
     * Helper method to create a new Aurex object with the given configuration key existing
     *
     * @param string $configKey
     *
     * @return Aurex
     */
    protected function makeAurex($configKey)
    {
        return new Aurex(new DevEnvironment, new Provider, [$configKey => []]);
    }
}