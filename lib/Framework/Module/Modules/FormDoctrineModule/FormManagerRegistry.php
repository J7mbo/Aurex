<?php

namespace Aurex\Framework\Module\Modules\FormDoctrineModule;

use Doctrine\Common\Persistence\AbstractManagerRegistry,
    Silex\Application as Application;

/**
 * Class FormManagerRegistry
 *
 * This class is used for connecting Symfony/Form to Doctrine entities
 *
 * @package Aurex\Framework\Module\Modules\FormDoctrineModule
 */
class FormManagerRegistry extends AbstractManagerRegistry
{
    /**
     * @var Application
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    protected function getService($name)
    {
        return $this->container[$name];
    }

    /**
     * {@inheritdoc}
     */
    protected function resetService($name)
    {
        unset($this->container[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAliasNamespace($alias)
    {
        throw new \BadMethodCallException('Namespace aliases not supported.');
    }

    /**
     * Set the container
     *
     * @param Application $container
     *
     * @return $this
     */
    public function setContainer(Application $container)
    {
        $this->container = $container['orm.ems'];

        return $this;
    }
}