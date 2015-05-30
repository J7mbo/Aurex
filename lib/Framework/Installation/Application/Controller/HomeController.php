<?php

namespace Aurex\Application\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils,
    Aurex\Application\Model\Form\Type\LoginFormType,
    Aurex\Framework\AbstractController;

/**
 * Class HomeController
 *
 * @package Aurex\Application\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @return array
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @param AuthenticationUtils $utils
     * @param LoginFormType       $type
     *
     * @return array
     */
    public function loginAction(AuthenticationUtils $utils, LoginFormType $type)
    {
        /** If the user is already logged in, forward them to the homepage **/
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            return $this->redirect('home');
        }

        /** Create the form **/
        $loginForm = $this->formFactory->getSymfonyFormFactory()->createBuilder($type, null, [
            'action' => $this->urlGenerator->generate('login_check')
        ])->getForm()->createView();

        return [
            'login_form'    => $loginForm,
            'error'         => $utils->getLastAuthenticationError(),
            'last_username' => $utils->getLastUsername(),
        ];
    }
}
