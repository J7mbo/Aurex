<?php

namespace Aurex\Application\Model\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\AbstractType;

/**
 * Class LoginFormType
 *
 * Used to create the Login Form
 *
 * @package Aurex\Application\Model\Form\Type
 */
class LoginFormType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('_username', 'text', ['label' => 'Email'])
                ->add('_password', 'password')
                ->add('submit', 'submit', ['label' => 'Login'])
                ->setMethod('POST');
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'intention' => 'authenticate'
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @note This is null so that the twig form renders the inputs as _username|_password instead of login[_username]
     */
    public function getName()
    {
        return null;
    }
}