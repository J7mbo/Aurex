<?php

namespace Aurex\Framework\Environment;

use Aurex\Framework\Aurex;

/**
 * Class DevEnvironment
 *
 * Represents the default development environment - xdebug ini settings are updated here
 *
 * @package Aurex\Framework\Environment
 */
class DevEnvironment implements EnvironmentInterface
{
    /**
     * {@inheritDoc}
     */
    public function perform(Aurex $aurex)
    {
        if (extension_loaded('xdebug'))
        {
            ini_set('display_errors', true);
            ini_set('xdebug.var_display_max_depth', 100);
            ini_set('xdebug.var_display_max_children', 100);
            ini_set('xdebug.var_display_max_data', 100);
        }

        $aurex['debug'] = true;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return 'dev';
    }
}