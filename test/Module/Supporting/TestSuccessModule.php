<?php

namespace Test\Module\Supporting;

use Aurex\Framework\Module\ModuleInterface,
    Aurex\Framework\Aurex;

/**
 * Class TestSuccessModule
 *
 * @package Test\Module\Supporting
 *
 * @see ModuleLoaderTest::testCanLoadCustomModule()
 */
class TestSuccessModule implements ModuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function integrate(Aurex $application) { }

    /**
     * {@inheritDoc}
     */
    public function usesConfigurationKey()
    {
        return 'test';
    }
}