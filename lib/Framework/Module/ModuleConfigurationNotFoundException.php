<?php

namespace Aurex\Framework\Module;

/**
 * Class ModuleConfigurationNotFoundException
 *
 * Thrown when a module states it requires a configuration, but the configuration key can't be found
 *
 * @package Aurex\Framework\Module
 */
class ModuleConfigurationNotFoundException extends \Exception { }