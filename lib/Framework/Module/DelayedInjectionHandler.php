<?php

namespace Aurex\Framework\Module;

use Symfony\Bridge\Twig\Extension\SecurityExtension,
    Silex\Application,
    Auryn\Injector;

/**
 * Class DelayedInjectionHandler
 *
 * Used for any injections that are not to be shared until the application is booted
 *
 * @package Aurex\Framework\Module
 */
class DelayedInjectionHandler
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @constructor
     *
     * @param Application $application
     * @param Injector    $injector
     */
    public function __construct(Application $application, Injector $injector)
    {
        $this->application = $application;
        $this->injector    = $injector;
    }

    /**
     * @param array $injections Single dimensional array of shareable values from the Silex\Application (pimple) object
     *
     * @return void
     */
    public function performInjections(array $injections)
    {
        foreach ($injections as $shareable)
        {
            $this->injector->share($this->application[$shareable]);
        }

        /**
         * This can only be done here because security / user is only read to be read after boot
         */
        if (isset($this->application['twig']) && isset($this->application['security.authorization_checker']))
        {
            $this->application['twig']->addExtension(new SecurityExtension($this->application['security.authorization_checker']));
        }
    }
}