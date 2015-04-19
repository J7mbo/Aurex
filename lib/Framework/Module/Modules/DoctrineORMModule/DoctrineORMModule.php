<?php

namespace Aurex\Framework\Module\Modules\DoctrineORMModule;

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider,
    Doctrine\ORM\Mapping\Driver\AnnotationDriver,
    Doctrine\Common\Annotations\AnnotationReader,
    Silex\Provider\DoctrineServiceProvider,
    Aurex\Framework\Module\ModuleInterface,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\Common\Cache\ApcCache,
    Doctrine\ORM\Configuration,
    Doctrine\DBAL\Connection,
    Aurex\Framework\Aurex;

/**
 * Class DoctrineORMModule
 *
 * @package Aurex\Framework\Module\Modules\DoctrineOrmModule
 */
class DoctrineORMModule implements ModuleInterface
{
    /**
     * @var string The configuration yaml key to read
     */
    const CONFIG_KEY = 'doctrine_orm_module';

    /**
     * {@inheritDoc}
     */
    public function integrate(Aurex $aurex)
    {
        $config = $aurex->getConfiguration(self::CONFIG_KEY);

        $aurex->register(new DoctrineServiceProvider, ['db.options' => $config]);

        $configType      = 'annotation';
        $entityDirectory = __DIR__ . '/../../../Application/Model/Entity';
        $entityNamespace = 'Aurex\Application\Model\Entity';
        $cacheObject     = !$aurex['debug'] && extension_loaded('apc') ? new ApcCache() : new ArrayCache();
        $cacheDirectory  = __DIR__ . '/../../../Application/Cache/Doctrine';
        $cacheNamespace  = 'Aurex\Application\Cache\Doctrine';
        $mappings        = [
            'mappings' => [
                [
                    'type'      => $configType,
                    'path'      => $entityDirectory,
                    'namespace' => $entityNamespace
                ]
            ]
        ];

        $aurex->register(new DoctrineOrmServiceProvider, [
            'orm.proxies_dir'           => $cacheDirectory,
            'orm.proxies_namespace'     => $cacheNamespace,
            'orm.cache'                 => $cacheObject,
            'orm.auto_generate_proxies' => true,
            'orm.em.options'            => $mappings
        ]);

        /** @var Connection $orm */
        $orm = $aurex['orm.em'];

        /** @var Configuration $ormConfig */
        $ormConfig = $orm->getConfiguration();

        $ormConfig->setMetadataDriverImpl(new AnnotationDriver(new AnnotationReader, [$entityDirectory]));

        $aurex->getInjector()->share($aurex['orm.em']);
        $aurex->getInjector()->share($aurex['db']);
    }

    /**
     * Defines the array key from the YAML configuration to use for it's own configuration
     *
     * @return string They key name. If null, no key (and so no configuration) is used
     */
    public function usesConfigurationKey()
    {
        return self::CONFIG_KEY;
    }
}