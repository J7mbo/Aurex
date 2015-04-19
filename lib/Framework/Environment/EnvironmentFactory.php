<?php

namespace Aurex\Framework\Environment;

/**
 * Class EnvironmentFactory
 *
 * @package Aurex\Framework\Environment
 */
class EnvironmentFactory
{
    /**
     * @var string The naming convention required for Environments
     */
    const ENV_MATCH = '(\w+)Environment';

    /**
     * @var string Namespace used for Environment object creation
     */
    const ENVIRONMENTS_NAMESPACE = 'Aurex\Framework\Environment';

    /**
     * @param string $environmentName The environment name to load
     * @param string $directory       Optional directory to load new environments from
     *
     * @throws EnvironmentNotFoundException
     *
     * @return EnvironmentInterface
     *
     * @note static:: so constants can be overridden
     */
    public function create($environmentName, $directory = __DIR__)
    {
        if ($directory === null)
        {
            $directory = __DIR__;
        }

        $availableEnvironments = [];

        try
        {
            $iterator = new \DirectoryIterator($directory);
        }
        catch (\Exception $e)
        {
            throw new EnvironmentNotFoundException(sprintf('The directory: %s was not found or readable', $directory));
        }

        foreach ($iterator as $file)
        {
            preg_match(sprintf('#%s#', static::ENV_MATCH), $file->getFilename(), $matches);

            if (isset($matches[0]))
            {
                $availableEnvironments[] = strtolower(str_replace(['Environment', '.php'], '', $file->getFilename()));
            }
        }

        $objectName = sprintf('%s\%sEnvironment', self::ENVIRONMENTS_NAMESPACE, ucfirst($environmentName));

        if (!in_array(strtolower($environmentName), $availableEnvironments) || !class_exists($objectName))
        {
            throw new EnvironmentNotFoundException(sprintf(
               'Environment provided but class not found for: %s', $environmentName
           ));
        }

        /** @var EnvironmentInterface */
        $environment = new $objectName;

        if (!($environment instanceof EnvironmentInterface))
        {
            throw new EnvironmentNotFoundException(sprintf(
               'Environment: %s does not implement EnvironmentInterface', $objectName
           ));
        }

        if ((string)$environment !== $environmentName)
        {
            throw new EnvironmentNotFoundException(sprintf(
               'Environment: %s found, but name: %s does not match factory provided name: %s',
               get_class($environment), (string)$environment, $environmentName
           ));
        }

        return $environment;
    }
}