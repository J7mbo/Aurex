<?php

namespace Aurex\Framework\Module\Modules\RoutingModule;

use Silex\Route;

/**
 * Class TemplateRouter
 *
 * Allows parsing of the 'template' parameter from routes.yml
 *
 * @package Aurex\Framework\Module\Modules\RoutingModule
 */
class TemplateRoute extends Route
{
    /**
     * @param string $path
     *
     * @return $this
     */
    public function template($path)
    {
        $this->setOption('template', $path);

        return $this;
    }
}