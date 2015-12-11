<?php

namespace Araneum\Bundle\AgentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LeadType
 *
 * @package Araneum\Bundle\AgentBundle\Form\Type
 */
class LeadType extends AbstractType
{
    /**
     * Build Lead Form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('country')
            ->add('phone')
            ->add('email')
            ->add('appKey');
    }

    /**
     * Configure Lead Form
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'class' => '\Araneum\Bundle\AgentBundle\Entity\Lead',
                'csrf_protection' => false,
            ]
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'araneum_agent_lead';
    }
}
