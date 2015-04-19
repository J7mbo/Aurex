<?php

namespace Aurex\Framework\Module;

use Aurex\Framework\Aurex;

/**
 * Class ModuleLoader
 *
 * Responsible for loading modules into Aurex
 *
 * @package Framework\ModuleLoader
 */
class ModuleLoader
{
    /**
     * @var string
     */
    const DEFAULT_NAMESPACE = 'Aurex\Framework\Module\Modules';

    /**
     * Load the module
     *
     * @param Aurex  $aurex
     * @param string $moduleName Either the module name if from the default namespace or fully qualified module
     *
     * @throws ModuleNotFoundException
     * @throws ModuleConfigurationNotFoundException
     *
     * @return void
     */
    public function load(Aurex $aurex, $moduleName)
    {
        $defaultModulePath = sprintf('%s\%s\%s', self::DEFAULT_NAMESPACE, $moduleName, $moduleName);

        /** Check for default modules or user provided ones **/
        if (!class_exists($defaultModulePath) && !class_exists($moduleName))
        {
            throw new ModuleNotFoundException(sprintf('Unable to load module: %s - it does not exist', $moduleName));
        }

        /** @var ModuleInterface $module */
        $module = class_exists($moduleName) ? $aurex->getInjector()->make($moduleName) : new $defaultModulePath;

        if (!($module instanceof ModuleInterface))
        {
            throw new ModuleNotFoundException(sprintf('Module: %s does not implement ModuleInterface', $moduleName));
        }

        $configuration    = $aurex->getConfiguration();
        $configurationKey = $module->usesConfigurationKey();

        if ($configurationKey !== null && (strlen($configurationKey) > 0 && !array_key_exists($configurationKey, $configuration)))
        {
            throw new ModuleConfigurationNotFoundException(sprintf(
                'Module: %s requires configuration key: %s', $moduleName, $configurationKey
            ));
        }

        /** Perform the module's individual logic **/
        $module->integrate($aurex);

        /** Store a list of which modules have been loaded **/
        $aurex->addLoadedModule($module);
    }
}