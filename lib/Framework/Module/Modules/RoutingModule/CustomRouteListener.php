<?php

namespace Aurex\Framework\Module\Modules\RoutingModule;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent,
    Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\HttpKernel\KernelEvents,
    Symfony\Component\HttpFoundation\Response,
    Silex\Application as SilexApplication,
    Silex\Route;

/**
 * Class CustomRouteListener
 *
 * Allows the use of 'template' within routes file (thanks Dave Marshall)
 *
 * @see http://davedevelopment.co.uk/2012/11/26/silex-route-helpers-for-a-cleaner-architecture.html
 *
 * @package Aurex\Framework\Module\Modules\RoutingModule
 */
class CustomRouteListener implements EventSubscriberInterface
{
    /**
     * @var SilexApplication
     */
    protected $app;

    /**
     * @constructor
     *
     * @param SilexApplication $app
     */
    public function __construct(SilexApplication $app)
    {
        $this->app = $app;
    }

    /**
     * Used for template option
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();

        if (!is_array($response))
        {
            return;
        }

        $request   = $event->getRequest();
        $routeName = $request->attributes->get('_route');

        /** @var Route $route */
        if (!$route = $this->app['routes']->get($routeName))
        {
            return;
        }

        if (!$template = $route->getOption('template'))
        {
            return;
        }

        $output = $this->app['twig']->render($template, $response);

        $event->setResponse(new Response($output));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::VIEW => ['onKernelView', -10]];
    }
}