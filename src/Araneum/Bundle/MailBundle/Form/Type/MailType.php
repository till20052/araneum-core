<?php

namespace Araneum\Bundle\MailBundle\Form\Type;

use Araneum\Bundle\MailBundle\Entity\Mail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

/**
 * Class MailType
 *
 * @package Araneum\Bundle\MailBundle\Form\Type
 */
class MailType extends AbstractType
{
    protected $router;

    /**
     * UserAdminType constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

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
                'sender',
                'text',
                [
                    'label' => 'Sender',
                    'attr' => [
                        'placeholder' => 'mails.PLACEHOLDER',
                        'translateLabel' => 'mails.SENDER',
                    ],
                ]
            )
            ->add(
                'target',
                'text',
                [
                    'label' => 'Target',
                    'attr' => [
                        'placeholder' => 'mails.PLACEHOLDER',
                        'translateLabel' => 'mails.TARGET',
                    ],
                ]
            )
            ->add(
                'headline',
                'text',
                [
                    'label' => 'Headline',
                    'attr' => [
                        'placeholder' => 'mails.PLACEHOLDER',
                        'translateLabel' => 'mails.HEADLINE',
                    ],
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'label' => 'Orientation',
                    'choices' => Mail::$statuses,
                    'empty_value' => 'mails.EMPTY_VALUE',
                    'attr' => [
                        'translateLabel' => 'mails.STATUS',
                    ],
                ]
            )
            ->add(
                'sentAt',
                'date',
                [
                    'label' => 'Sent at',
                    'widget' => 'single_text',
                    'attr' => [
                        'translateLabel' => 'mails.SENT_AT',
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
                'data_class' => 'Araneum\Bundle\MailBundle\Entity\Mail',
                'csrf_protection' => false,
            ]
        );
    }
}
