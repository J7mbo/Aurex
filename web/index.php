<?php

require_once __DIR__ . '/../vendor/autoload.php';

/** Read configuration, cached or otherwise **/
$parser       = new \Aurex\Framework\Config\Parser(new \Symfony\Component\Yaml\Parser);
$cacher       = (new \Aurex\Framework\Config\ParserCacherFactory)->make();
$parserCacher = new \Aurex\Framework\Config\ParserCacher($parser, $cacher);

$confDir = __DIR__ . '/../lib/Application/Config/';
$config  = $parserCacher->parseConfig($confDir . 'global.yml');

/** Create relevant Environment **/
$environment = (new \Aurex\Framework\Environment\EnvironmentFactory)->create(
    $config['environment']['name'], $config['environment']['dir'] === '~' ? null : $config['environment']['dir']
);

/** Parse environment-specific configuration file **/
$configFile = sprintf('%s%s.yml', $confDir, $config['environment']['name']);
$config     = array_merge($config, $parserCacher->parseConfig($configFile));

/** Parse routes file **/
$routesFile = $confDir . 'routes.yml';
$config     = array_merge($config, $parserCacher->parseConfig($routesFile));

/** Parse security file **/
$securityFile = $confDir . 'security.yml';
$config       = array_merge($config, $parserCacher->parseConfig($securityFile));

/** Create the Auryn Dependency Injector **/
$injector = new \Auryn\Provider(new \Auryn\ReflectionPool);

/** Create the object that decorates the Silex application **/
$aurex = new \Aurex\Framework\Aurex($environment, $injector, $config);

/** Perform environment-specific changes to Aurex **/
$environment->perform($aurex);

/** Plug and play all the modules **/
$loader = new \Aurex\Framework\Module\ModuleLoader;

foreach ($config['modules'] as $moduleName)
{
    $loader->load($aurex, $moduleName);
}

/** Custom bootstrapping **/
require_once __DIR__ . '/../lib/Application/index.php';

/** Doctrine cli-config also uses this bootstrap for db etc, so don't run HTTP stuff if cli is being used **/
isset($cli) ?: $aurex->run();