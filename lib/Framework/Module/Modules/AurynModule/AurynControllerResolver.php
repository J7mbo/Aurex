<?php

namespace Aurex\Framework\Module\Modules\AurynModule;

use Aurex\Framework\Module\DelayedInjectionHandler;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,
    Aurex\Framework\Aurex,
    Auryn\Injector;

/**
 * Class AurynControllerResolver
 *
 * Controller resolver decorating the default controller resolver to use the Auryn DiC
 *
 * Case: 'ClassName:methodName'  - Use Auryn Injector
 * Case: 'ClassName::methodName' - Use original controller resolver (pimple)
 *
 * @package Aurex\Framework\Module\Modules\AurynModule
 */
class AurynControllerResolver implements ControllerResolverInterface
{
    /**
     * @var string The pattern for resolver delegation
     */
    const SERVICE_PATTERN = "/[A-Za-z0-9\._\-]+:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/";

    /**
     * @var ControllerResolverInterface
     */
    protected $resolver;

    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @var Aurex
     */
    protected $aurex;

    /**
     * @var DelayedInjectionHandler
     */
    protected $delayHandler;

    /**
     * @var array
     */
    protected $config;

    /**
     * @constructor
     *
     * @param ControllerResolverInterface $resolver     Controller resolver to decorate
     * @param Injector                    $injector     Auryn injector to use instead
     * @param Aurex                       $aurex        The Silex Application for pimple
     * @param DelayedInjectionHandler     $delayHandler The handler for objects shareable only after boot
     * @param array                       $config       Application configuration values (mainly for DiC)
     */
    public function __construct(
        ControllerResolverInterface $resolver,
        Injector                    $injector,
        Aurex                       $aurex,
        DelayedInjectionHandler     $delayHandler,
        array                       $config
    )
    {
        $this->resolver     = $resolver;
        $this->injector     = $injector;
        $this->aurex        = $aurex;
        $this->delayHandler = $delayHandler;
        $this->config       = $config;
    }

    /**
     * Executes the controller / action with either Auryn or default resolver
     *
     * @param Request $request Request object
     *
     * @return Response Response object
     */
    public function getController(Request $request)
    {
        $controllerAction = $request->attributes->get('_controller', null);

        /** Fall back to Symfony controller resolver **/
        if (!is_string($controllerAction) || !preg_match(static::SERVICE_PATTERN, $controllerAction))
        {
            return $this->resolver->getController($request);
        }

        list($controller, $action) = explode(':', sprintf('%s', $controllerAction), 2);

        /** These objects are only available for DI after the application has been booted, and it has been booted at this point **/
        $delayInjections = isset($this->config['boot_delay_injections']) ? $this->config['boot_delay_injections'] : [];
        $this->delayHandler->performInjections($delayInjections);

        $routeParams = $request->attributes->all()['_route_params'];
        $params = !empty($routeParams) ? $routeParams : [];

        $args = [];

        /** Prefix 'slugs' with a : as Auryn requires for scalar DI in controllers (symfony-style) **/
        array_walk($params, function($value, $key) use (&$args) {
            $args[sprintf(':%s', $key)] = $value;
        });

        /** Executed by the HTTP Kernel **/
        return function () use ($controller, $action, $args) {
            return $this->injector->execute([$this->injector->make($controller), $action], $args);
        };
    }

    /**
     * Original resolver delegation
     *
     * {@inheritDoc}
     */
    public function getArguments(Request $request, $controller)
    {
        return $this->resolver->getArguments($request, $controller);
    }
}