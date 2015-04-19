<?php

/** vendor/bin/doctrine looks for a config/cli-config.php file in the project - so that's why this is here **/

/** If the bootstrap (Aurex.php) sees this variable, don't 'run' the HTTP front-controller **/
$cli = true;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/web/index.php';

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain,
    Symfony\Component\Console\Application as CliApplication,
    Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper,
    Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper,
    UseAllFive\Command\LoadDataFixturesDoctrineCommand,
    Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\ORM\Mapping\Driver\AnnotationDriver,
    Doctrine\Common\Annotations\AnnotationReader,
    Symfony\Component\Console\Helper\HelperSet,
    Doctrine\ORM\Mapping\Driver\DatabaseDriver,
    Doctrine\ORM\Tools\Console\Command,
    Doctrine\DBAL\Connection,
    Doctrine\DBAL\Version;

/** @var Connection $db The above bootstrap creates the app object for us */
$db = $aurex['db'];

/** @var Doctrine\ORM\EntityManager $em The entity manager */
$em = $aurex['orm.em'];

$driver = new DatabaseDriver($db->getSchemaManager());
$driver->setNamespace('Aurex\Application\Entity\\');

$annotationsFile = __DIR__ . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php';
AnnotationRegistry::registerFile($annotationsFile);

$driverChain = new MappingDriverChain();
$driverChain->addDriver(
    new AnnotationDriver(new AnnotationReader(), [__DIR__ . '/lib/Application/Model/Entity']), 'Aurex\Application\\Model\Entity\\'
);

$em->getConfiguration()->setMetadataDriverImpl($driverChain);

/** @var Symfony\Component\Console\Application $cli */
$cli = new CliApplication('Doctrine Command Line Interface', Version::VERSION);
$cli->setCatchExceptions(true);

$cli->setHelperSet(new HelperSet([
    'db' => new ConnectionHelper($em->getConnection()),
    'em' => new EntityManagerHelper($em)
]));

$cli->addCommands([
    new Command\GenerateRepositoriesCommand,
    new Command\GenerateEntitiesCommand,
    new Command\ConvertMappingCommand,
    new Command\ValidateSchemaCommand,
    new Command\SchemaTool\CreateCommand,
    new Command\SchemaTool\UpdateCommand,
    new Command\GenerateProxiesCommand,
    new LoadDataFixturesDoctrineCommand
]);

$cli->run();