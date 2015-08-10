<?php

namespace Aurex\Framework\Module\Modules\TwigModule;

use Symfony\Component\HttpFoundation\RequestStack,
    Aurex\Framework\Module\ModuleInterface,
    Silex\Provider\TwigServiceProvider,
    Aurex\Framework\Aurex;

/**
 * Class TwigModule
 *
 * @package Aurex\Framework\Module\Modules\TwigModule
 */
class TwigModule implements ModuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function integrate(Aurex $aurex)
    {
        $aurex->register(new TwigServiceProvider, [
            'cache'        => __DIR__ . '/../../../Application/Cache/Twig',
            'twig.path'    => __DIR__ . '/../../../../../web/templates',
            'twig.options' => ['debug' => ((string)($aurex->getEnvironment() === "dev") ? true : false)]
        ]);

        $this->addAssetFunction($aurex);

        $aurex->getInjector()->share($aurex['twig']);
        $aurex->getInjector()->share($aurex['url_generator']);
    }

    /**
     * Add the ability to call {{ asset() }} within Twig templates
     *
     * @param Aurex $aurex
     */
    protected function addAssetFunction(Aurex $aurex)
    {
        $aurex['twig'] = $aurex->extend('twig', function($twig, $aurex) {
            /** @var \Twig_Environment $twig */
            $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($aurex) {
                /** @var RequestStack $requestStack */
                $requestStack = $aurex['request_stack'];
                return sprintf('%s/assets/%s', $requestStack->getCurrentRequest()->getBasePath(), ltrim($asset, '/'));
            }));

            return $twig;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function usesConfigurationKey()
    {
        return null;
    }
}