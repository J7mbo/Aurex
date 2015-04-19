<?php

namespace Aurex\Framework\Module\Modules\RoutingModule;

use Aurex\Framework\Module\ModuleInterface,
    Aurex\Framework\Aurex;

/**
 * Class RoutingModule
 *
 * @package Aurex\Framework\Module\Modules\RoutingModule
 */
class RoutingModule implements ModuleInterface
{
    /**
     * @var string The controllers namespace
     */
    const CONTROLLER_NAMESPACE = 'Aurex\Application\Controller';

    /**
     * @var string The configuration yaml key to read
     */
    const CONFIG_KEY = 'routing_module';

    /**
     * {@inheritDoc}
     */
    public function integrate(Aurex $aurex)
    {
        $aurex['route_class'] = 'Aurex\Framework\Module\Modules\RoutingModule\TemplateRoute';
        $aurex['dispatcher']->addSubscriber(new CustomRouteListener($aurex));

        $routes = $aurex->getConfiguration(self::CONFIG_KEY) ?: [];

        foreach ($routes as $name => $route)
        {
            $controllerName = sprintf('%s\%s', self::CONTROLLER_NAMESPACE, $route['controller']);

            /** Match the controller class to the route, and bind a name accessible to urlGenerator and twig **/
            $controller = $aurex->match($route['pattern'], $controllerName)->bind($name);

            /** Map a HTTP Method requirement to this route that defaults to GET if none is provided **/
            $controller->method(isset($route['method']) ? $route['method'] : 'GET');

            /** Map a twig template to the route if one is provided so that the route only needs to return an array **/
            if (isset($route['template']))
            {
                /** @var TemplateRoute $controller */
                $controller->template($route['template']);
            }

            /** Add any slug requirements to the route as regex that must be matched for each slug **/
            if (isset($route['regex']))
            {
                foreach ($route['regex'] as $slugName => $regexRequirement)
                {
                    $controller->assert($slugName, $regexRequirement);
                }
            }

            /** Add any default slug variables if none are provided **/
            if (isset($route['default']))
            {
                foreach ($route['default'] as $slugName => $defaultValue)
                {
                    $controller->value($slugName, $defaultValue);
                }
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