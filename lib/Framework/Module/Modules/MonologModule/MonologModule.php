<?php

namespace Aurex\Framework\Module\Modules\MonologModule;

use Aurex\Framework\Module\ModuleInterface,
    Silex\Provider\MonologServiceProvider,
    Aurex\Framework\Aurex;

/**
 * Class MonologModule
 *
 * @package Aurex\Framework\Module\Modules\MonologModule
 */
class MonologModule implements ModuleInterface
{
    /**
     * @var string The configuration yaml key to read
     */
    const CONFIG_KEY = 'monolog_module';

    /**
     * @var string The default name associated with each log entry within the logging file
     */
    const DEFAULT_LOG_NAME = 'aurex';

    /**
     * @var string The default aurex logging file
     */
    const DEFAULT_LOG_FILE = '/var/log/aurex.log';

    /**
     * {@inheritDoc}
     */
    public function integrate(Aurex $aurex)
    {
        $config = $aurex->getConfiguration(self::CONFIG_KEY);

        $logName = ($config['log_name'] === null) ? self::DEFAULT_LOG_NAME : $config['log_name'];
        $logFile = ($config['log_file'] === null) ? self::DEFAULT_LOG_FILE : $config['log_file'];

        $aurex->register(new MonologServiceProvider, [
            'monolog.name'    => $logName,
            'monolog.logfile' => $logFile
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function usesConfigurationKey()
    {
        return self::CONFIG_KEY;
    }
}