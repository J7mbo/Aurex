<?php

namespace Aurex\Framework\Environment;

use Aurex\Framework\Aurex;

/**
 * Class NameEnvironment
 *
 * @package Aurex\Framework\Environment
 */
class NameEnvironment implements EnvironmentInterface
{
    /**
     * {@inheritDoc}
     */
    public function perform(Aurex $aurex)
    {
        /** -- SNIP -- */
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return 'wrongName!!';
    }
}