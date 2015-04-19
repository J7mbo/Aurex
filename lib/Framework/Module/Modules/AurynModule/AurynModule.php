<?php

namespace Aurex\Framework\Module\Modules\AurynModule;

use Aurex\Framework\Module\DelayedInjectionHandler,
    Aurex\Framework\Module\ModuleInterface,
    Aurex\Framework\Aurex;

/**
 * Class AurynModule
 *
 * @package Aurex\Framework\Module\Modules\AurynModule
 */
class AurynModule implements ModuleInterface
{
    /**
     * @var string The configuration yaml key to read
     */
    const CONFIG_KEY = 'auryn_module';

    /**
     * @var Aurex
     */
    private $aurex;

    /**
     * {@inheritDoc}
     */
    public function integrate(Aurex $aurex)
    {
        $this->aurex = $aurex;
        $injector    = $aurex->getInjector();
        $config      = $aurex->getConfiguration();

        /** Alias any interfaces to concrete implementations **/
        $this->aliasObjects();

        /** Delegate any object creations to closures or factories **/
        $this->delegateObjects();

        /** Share any non-silex objects **/
        $this->shareObjects();

        /** Handler for any injection definition sharing for objects that only exist after application boot **/
        $handler = new DelayedInjectionHandler($aurex, $injector);

        /** Have Auryn instantiate our controllers and run the methods **/
        $aurex['resolver'] = $aurex->extend('resolver', function ($resolver, $app) use ($injector, $config, $handler) {
            return new AurynControllerResolver($resolver, $injector, $app, $handler, $config);
        });
    }

    /**
     * Alias any interfaces or abstracts to concrete objects so that when these are encountered the relevant alias'd
     * objected is instantiated and used polymorphically
     */
    public function aliasObjects()
    {
        $injector = $this->aurex->getInjector();
        $config   = $this->aurex->getConfiguration(self::CONFIG_KEY);

        if (isset($config['alias']))
        {
            foreach ($config['alias'] as $alias => $typehintToReplace)
            {
                $injector->alias($typehintToReplace, $alias);
            }
        }
    }

    /**
     * Delegate the instantiation of any objects to their relevant factories
     */
    public function delegateObjects()
    {
        $injector = $this->aurex->getInjector();
        $config   = $this->aurex->getConfiguration(self::CONFIG_KEY);

        if (isset($config['delegate']))
        {
            foreach($config['delegate'] as $delegator => $delegate)
            {
                /** Are there any scalar arguments provided in the config for the delegate? **/
                if (!is_array($delegate))
                {
                    $injector->delegate($delegator, $delegate);
                }
                else
                {
                    /** Pre-pend a : for scalar parameter injection as Auryn requires **/
                    $newDelegateArray = [];

                    foreach ($delegate as $key => $array)
                    {
                        foreach ($array as $scalar => $objectDelegate)
                        {
                            $newDelegateArray[$key] = [':' . $scalar => $objectDelegate];
                        }
                    }

                    /** Configuration provided **/
                    $injector->delegate($delegator, key($delegate), reset($newDelegateArray));
                }
            }
        }
    }

    /**
     * Share any non-Silex objects around the application (the string is shared)
     */
    public function shareObjects()
    {
        $injector = $this->aurex->getInjector();
        $config   = $this->aurex->getConfiguration(self::CONFIG_KEY);

        if (isset($config['share']))
        {
            foreach ($config['share'] as $shareable)
            {
                $injector->share($shareable);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function usesConfigurationKey()
    {
        return self::CONFIG_KEY;
    }
}