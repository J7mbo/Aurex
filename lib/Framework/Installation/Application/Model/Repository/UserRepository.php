<?php

namespace Aurex\Application\Model\Repository;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\Security\Core\Exception\UnsupportedUserException,
    Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\User\UserInterface,
    Aurex\Application\Model\Entity\User,
    Doctrine\ORM\NoResultException,
    Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 *
 * @package Aurex\Application\Model\Repository
 */
class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        return $this->loadUserByEmail($username);
    }

    /**
     * @see loadUserByUsername
     *
     * @param string $email
     *
     * @throws UsernameNotFoundException
     *
     * @return User
     */
    public function loadUserByEmail($email)
    {
        $q = $this->createQueryBuilder('u')
                  ->select('u, r')
                  ->leftJoin('u.roles', 'r')
                  ->where('u.email = :email')
                  ->setParameter('email', $email)
                  ->getQuery();

        try
        {
            $user = $q->getSingleResult();
        }
        catch (NoResultException $e)
        {
            throw new UsernameNotFoundException(sprintf('Email "%s" does not exist', $email));
        }

        $newUser = new User(
            $user->getUsername(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getRoles(),
            $user->isEnabled()
        );

        return $newUser->setId($user->getId());
    }

    /**
     * Used automatically from the silex login process
     *
     * @param $id
     *
     * @return User
     */
    public function loadUserById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByEmail($user->getEmail());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
