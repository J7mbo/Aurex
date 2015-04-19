<?php

namespace Aurex\Application\Model\Fixture;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\DataFixtures\FixtureInterface,
    Doctrine\Common\Persistence\ObjectManager,
    Aurex\Application\Model\Entity\Role;

/**
 * Class LoadRoles
 *
 * Pre-populates the roles table
 *
 * @package Aurex\Application\Model\Fixture
 */
class LoadRoles implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * @var array The roles that will be inserted into the db
     */
    protected $roles = [
        [
            'name' => 'Admin',
            'role' => 'ROLE_ADMIN',
        ],
        [
            'name' => 'User',
            'role' => 'ROLE_USER'
        ]
    ];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->roles as $roleData)
        {
            $role = $this->findOrCreateRole($roleData['name'], $manager);

            /** Check if the object is managed (so already exists in the database) **/
            if (!$manager->contains($role))
            {
                $manager->persist($role);
            }
        }

        $manager->flush();
    }

    /**
     * Helper method to return an already existing Role from the database, else create and return a new one
     *
     * @param string        $name
     * @param ObjectManager $manager
     *
     * @return Role
     */
    protected function findOrCreateRole($name, ObjectManager $manager)
    {
        $roleData = reset(array_filter($this->roles, function($roleData) use ($name) {
            return $roleData['name'] === $name;
        }));

        return $manager->getRepository('Aurex\Application\Model\Entity\Role')
                       ->findOneBy(['name' => $name]) ?: new Role($name, $roleData['role']);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}