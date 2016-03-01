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
                        'translateLabel' => 'mails.HEADLINE',
                    ],
                ]
            )
            ->add(
                'textBody',
                'text',
                [
                    'label' => 'Body',
                    'attr' => [
                        'translateLabel' => 'mails.BODY',
                    ],
                ]
            )
            ->add(
                'status',
                'text',
                [
                    'label' => 'Status',
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
