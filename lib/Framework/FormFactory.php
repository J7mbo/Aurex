<?php

namespace Aurex\Framework;

use Symfony\Component\Form\FormFactory as SymfonyFormFactory,
    Symfony\Component\Form\AbstractType as Type,
    Symfony\Component\Form\FormView;

/**
 * Class FormFactory
 *
 * Factory for building a form from the given form Type so you don't have to do this every time in your controllers
 *
 * @package Aurex\Framework
 */
class FormFactory
{
    /**
     * @var SymfonyFormFactory
     */
    protected $formFactory;

    /**
     * @constructor
     *
     * @param SymfonyFormFactory $formFactory
     */
    public function __construct(SymfonyFormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * If you want the original Symfony FormFactory object
     *
     * @return SymfonyFormFactory
     */
    public function getSymfonyFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * Build the form object to return to the templating engine
     *
     * @param Type  $type
     * @param array $options
     *
     * @return FormView
     */
    public function buildFormFromType(Type $type, array $options = array())
    {
        return $this->formFactory->createBuilder($type, null, $options)->getForm()->createView();
    }
}