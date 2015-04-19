<?php

namespace Aurex\Framework\Environment;

use Aurex\Framework\Aurex;

/**
 * Interface EnvironmentInterface
 *
 * Represents an environment capable of performing any environmental changes should this be the selected environment and
 * can be identified by calling __toString() on the object
 *
 * @package Aurex\Framework\Environment
 */
interface EnvironmentInterface
{
    /**
     * Perform any required environment changes should this be the selected environment
     *
     * @param Aurex $aurex
     *
     * @return void
     */
    public function perform(Aurex $aurex);

    /**
     * @return string The implementing method should return the environment name, eg 'dev' or 'live'
     */
    public function __toString();
}