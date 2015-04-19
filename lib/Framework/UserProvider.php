<?php

namespace Aurex\Framework;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage,
    Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\HttpFoundation\Session\Session,
    Aurex\Application\Model\Entity\User,
    Doctrine\ORM\EntityManager;

/**
 * Class UserProvider
 *
 * @package Aurex\Framework
 */
class UserProvider
{
    /**
     * @var string The flash error message to be displayed to the user
     */
    const ERROR_MESSAGE = 'A logged-in user is required for this operation';

    /**
     * @var string The type of flash error message to display (can be error|warning|notice etc)
     */
    const ERROR_TYPE = 'error';

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @constructor
     *
     * @param TokenStorage    $tokenStorage
     * @param EntityManager   $entityManager
     * @param Session         $session
     */
    function __construct(TokenStorage $tokenStorage, EntityManager $entityManager, Session $session)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager   = $entityManager;
        $this->session         = $session;
    }

    /**
     * Get the currently logged in user
     *
     * @throws AccessDeniedHttpException When no currently logged in user exists in the session
     *
     * @return User
     */
    public function getUser()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof User)
        {
            $this->session->getFlashBag()->add(self::ERROR_TYPE, self::ERROR_MESSAGE);

            throw new AccessDeniedHttpException(self::ERROR_MESSAGE);
        }

        return $this->entityManager->merge($user);
    }
}