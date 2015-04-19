<?php

namespace Aurex\Application\Model\Fixture;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\DataFixtures\FixtureInterface,
    Doctrine\Common\Persistence\ObjectManager,
    Aurex\Application\Model\Entity\User,
    Aurex\Application\Model\Entity\Role;

/**
 * Class LoadUsers
 *
 * Pre-populates the users and user_roles tables
 *
 * @package Aurex\Application\Model\Fixture
 */
class LoadUsers implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * @var array The users that will be inserted in the database table - array key is row id
     */
    protected $users = [
        [
            'email'    => 'admin@aurex.com',
            'username' => 'admin',
            'password' => 'password',
            'roles'    => ['Admin']
        ]
    ];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->users as $userData)
        {
            $user = $this->findOrCreateUser($userData['email'], $manager);

            /** Check if the object is managed (so already exists in the database) **/
            if (!$manager->contains($user))
            {
                $manager->persist($user);
            }
        }

        $manager->flush();
    }

    /**
     * Helper method to return an already existing User from the database, else create and return a new one
     *
     * @param string        $email
     * @param ObjectManager $manager
     *
     * @return User
     */
    protected function findOrCreateUser($email, ObjectManager $manager)
    {
        $userRepo = $manager->getRepository('Aurex\Application\Model\Entity\User');
        $roleRepo = $manager->getRepository('Aurex\Application\Model\Entity\Role');

        $userData = reset(array_filter($this->users, function($userData) use ($email) {
            return $userData['email'] === $email;
        }));

        $username = $userData['username'];
        $password = $userData['password'];

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        $user = $userRepo->findOneBy(['email' => $email]) ?: new User($username, $email, $hash, $roles = []);

        /** Users are always given ROLE_USER within the repository anyway **/
        if (isset($userData['roles']))
        {
            foreach ($userData['roles'] as $roleName)
            {
                /** @var Role $role * */
                $role = $roleRepo->findOneBy(['name' => $roleName]);

                $user->addRole($role);
            }
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}