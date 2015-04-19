<?php

namespace Aurex\Framework\Module\Modules\FirewallModule;

use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder,
    Aurex\Application\Model\Repository\UserRepository,
    Aurex\Framework\Module\ModuleInterface,
    Silex\Provider\SecurityServiceProvider,
    Doctrine\ORM\Mapping\ClassMetadata,
    Doctrine\ORM\EntityManager,
    Aurex\Framework\Aurex;

/**
 * Class FirewallModule
 *
 * @package Aurex\Framework\Module\Modules\FirewallModule
 */
class FirewallModule implements ModuleInterface
{
    /**
     * @var string The User entity to be used for authorization
     */
    const USER_ENTITY = '\Aurex\Application\Model\Entity\User';

    /**
     * @var string The configuration yaml key to read
     */
    const CONFIG_KEY = 'firewall_module';

    /**
     * {@inheritDoc}
     */
    public function integrate(Aurex $aurex)
    {
        $config = $aurex->getConfiguration(self::CONFIG_KEY);

        $firewalls   = $config['firewalls'];
        $hierarchy   = $config['hierarchy'];
        $accessRules = $config['access_rules'];

        $firewalls['default']['users'] = function($aurex) {
            /** @var EntityManager $orm */
            $orm = $aurex['orm.em'];
            return new UserRepository($orm, new ClassMetadata('Aurex\Application\Model\Entity\User'));
        };

        $aurex->register(new SecurityServiceProvider, [
            'security.firewalls'      => $firewalls,
            'security.role_hierarchy' => $hierarchy,
            'security.access_rules'   => $accessRules
        ]);

        $aurex['security.encoder.digest'] = function() {
            return new BCryptPasswordEncoder(10);
        };
    }

    /**
     * {@inheritDoc}
     */
    public function usesConfigurationKey()
    {
        return self::CONFIG_KEY;
    }
}