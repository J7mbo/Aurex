<?php

namespace Aurex\Framework\Module;

use Aurex\Framework\Aurex;

/**
 * Interface ModuleInterface
 *
 * Describes a module that 'hooks-in', alters, then returns the altered application object
 *
 * @package Framework\ModuleLoader
 */
interface ModuleInterface
{
    /**
     * Modify anything within the Aurex or Silex application object
     *
     * @param Aurex $application The application object to modify
     *
     * @return Aurex The modified application object
     */
    public function integrate(Aurex $application);

    /**
     * Defines the array key from the YAML configuration to use for it's own configuration
     *
     * @return string They key name. If null, no key (and so no configuration) is used
     */
    public function usesConfigurationKey();
}