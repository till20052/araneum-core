<?php

namespace Araneum\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormTypeInterface;

class ProfileType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                'text',
                [
                    'label' => 'login',
                    'attr' => ['class' => ' form-control']
                ]
            )
            ->add(
                'fullName',
                'text',
                [
                    'required' => false,
                    'label' => 'full_name',
                    'attr' => ['class' => ' form-control']
                ]
            )
            ->add(
                'email',
                'text',
                [
                    'label' => 'email',
                    'attr' => ['class' => ' form-control']
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => ['Profile']
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'araneum_user_form_profile';
    }
}