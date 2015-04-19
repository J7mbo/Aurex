<?php

namespace Aurex\Framework;

use Aurex\Framework\Environment\EnvironmentInterface,
    Aurex\Framework\Module\ModuleInterface,
    Auryn\Provider as Injector,
    Silex\Application;

/**
 * Class Aurex
 *
 * @package Aurex\Framework
 */
class Aurex extends Application
{
    /**
     * @var EnvironmentInterface
     */
    protected $environment;

    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var string[]
     */
    protected $loadedModules = [];

    /**
     * @constructor
     *
     * @param EnvironmentInterface $environment
     * @param Injector             $injector
     * @param array                $configuration
     */
    public function __construct(EnvironmentInterface $environment, Injector $injector, array $configuration)
    {
        $this->environment   = $environment;
        $this->injector      = $injector;
        $this->configuration = $configuration;

        parent::__construct([]);
    }

    /**
     * @return EnvironmentInterface
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return Injector
     */
    public function getInjector()
    {
        return $this->injector;
    }

    /**
     * @param null|string $key Optional key to retrieve if it exists
     *
     * @return array|false False if the key provided does not exist
     */
    public function getConfiguration($key = null)
    {
        if (!is_null($key))
        {
            return array_key_exists($key, $this->configuration) ? $this->configuration[$key] : false;
        }

        return $this->configuration;
    }

    /**
     * @param ModuleInterface $module
     *
     * @return $this
     *
     * @note If a module doesn't have a configuration key then it's class name is used instead
     */
    public function addLoadedModule(ModuleInterface $module)
    {
        $configKey  = $module->usesConfigurationKey();
        $moduleName = ($configKey === null) ? (new \ReflectionClass($module))->getShortName() : $configKey;

        $this->loadedModules[$moduleName] = $module;

        return $this;
    }

    /**
     * @param ModuleInterface $module
     *
     * @return bool
     */
    public function moduleIsLoaded(ModuleInterface $module)
    {
        return array_key_exists($module->usesConfigurationKey(), $this->loadedModules);
    }
}