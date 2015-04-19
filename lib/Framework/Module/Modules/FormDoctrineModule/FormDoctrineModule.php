<?php

namespace Aurex\Framework\Module\Modules\FormDoctrineModule;

use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension,
    Silex\Provider\TranslationServiceProvider,
    Aurex\Framework\Module\ModuleInterface,
    Silex\Provider\SessionServiceProvider,
    Silex\Provider\FormServiceProvider,
    Aurex\Framework\Aurex;

/**
 * Class FormDoctrineModule
 *
 * @package Aurex\Framework\Module\Modules\FormDoctrineModule
 */
class FormDoctrineModule implements ModuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function integrate(Aurex $aurex)
    {
        $aurex->register(new FormServiceProvider);
        $aurex->register(new SessionServiceProvider);
        $aurex->register(new TranslationServiceProvider, [
            'locale' => 'en'
        ]);

        $aurex['form.extensions'] = $aurex->extend('form.extensions', function ($extensions, $app) {
            $managerRegistry = new FormManagerRegistry(null, [], ['default'], null, null, '\Doctrine\ORM\Proxy\Proxy');
            $managerRegistry->setContainer($app);
            unset($extensions);
            return [(new DoctrineOrmExtension($managerRegistry))];
        });

        $aurex->getInjector()->share($aurex['form.factory']);
    }

    /**
     * {@inheritDoc}
     */
    public function usesConfigurationKey()
    {
        return null;
    }
}