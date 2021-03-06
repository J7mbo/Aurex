<?php

namespace Aurex\Framework;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage,
    Symfony\Component\Security\Core\Authorization\AuthorizationChecker,
    Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\Routing\Generator\UrlGenerator,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\HttpFoundation\RequestStack,
    Symfony\Component\HttpFoundation\Request,
    Aurex\Application\Model\Entity\User,
    Doctrine\ORM\EntityManager;

/**
 * Class AbstractController
 *
 * Provides access to common controller requirements when extended from
 *
 * @package Aurex\Framework
 */
class AbstractController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @constructor
     *
     * @param RequestStack         $requestStack
     * @param Session              $session
     * @param FormFactory          $formFactory
     * @param UrlGenerator         $urlGenerator
     * @param UserProvider         $userProvider
     * @param EntityManager        $entityManager
     * @param AuthorizationChecker $authorizationChecker
     * @param TokenStorage         $tokenStorage
     */
    public function __construct(
        RequestStack         $requestStack,
        Session              $session,
        FormFactory          $formFactory,
        UrlGenerator         $urlGenerator,
        UserProvider         $userProvider,
        EntityManager        $entityManager,
        AuthorizationChecker $authorizationChecker,
        TokenStorage         $tokenStorage
    )
    {
        $this->request              = $requestStack->getCurrentRequest();
        $this->session              = $session;
        $this->formFactory          = $formFactory;
        $this->urlGenerator         = $urlGenerator;
        $this->userProvider         = $userProvider;
        $this->entityManager        = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage         = $tokenStorage;
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
        return $this->userProvider->getUser();
    }
}