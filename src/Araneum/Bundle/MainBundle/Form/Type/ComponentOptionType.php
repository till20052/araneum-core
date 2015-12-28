<?php

namespace Araneum\Bundle\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ComponentOptionType
 *
 * @package Araneum\Bundle\MainBundle\Form\Type
 */
class ComponentOptionType extends AbstractType
{
    /**
     * Build token of component options
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', 'text', ['label' => 'key'])
            ->add('value', 'text', ['label' => 'value']);
    }
}
