<?php

namespace Araneum\Bundle\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Araneum\Bundle\MainBundle\Entity\Locale;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LocaleType
 *
 * @package Araneum\Bundle\MainBundle\Form\Type
 */
class LocaleType extends AbstractType
{
    /**
     * @inheritdoc
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'id',
                'hidden',
                [
                    'mapped' => false,
                ]
            )
            ->add(
                'name',
                'text',
                [
                    'label' => 'Name',
                    'attr' => [
                        'placeholder' => 'Enter locale name',
                    ],
                ]
            )
            ->add(
                'locale',
                'text',
                [
                    'label' => 'Locale',
                    'attr' => [
                        'placeholder' => 'Enter locale',
                    ],
                ]
            )
            ->add(
                'enabled',
                'choice',
                [
                    'label' => 'Enabled',
                    'choices' => Locale::$enable,
                    'empty_value' => 'Choose line',
                ]
            )
            ->add(
                'orientation',
                'choice',
                [
                    'label' => 'Orientation',
                    'choices' => Locale::$orientations,
                    'empty_value' => 'Choose line',
                ]
            )
            ->add(
                'encoding',
                'text',
                [
                    'label' => 'Encoding',
                    'attr' => [
                        'placeholder' => 'Enter encoding',
                    ],
                ]
            );
    }

    /**
     * @inheritdoc
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Araneum\Bundle\MainBundle\Entity\Locale',
                'csrf_protection' => false,
            ]
        );
    }
}
