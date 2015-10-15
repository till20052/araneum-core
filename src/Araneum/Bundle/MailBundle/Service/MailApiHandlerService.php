<?php

namespace Araneum\Bundle\MailBundle\Service;

use Araneum\Base\Exception\InvalidFormException;
use Araneum\Bundle\MailBundle\Entity\Mail;
use Araneum\Bundle\MailBundle\Form\Api\MailType;
use Araneum\Bundle\MainBundle\Service\ApplicationManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;

class MailApiHandlerService
{
    protected $manager;
    protected $formFactory;
    /** @var  ApplicationManager */
    protected $applicationManager;

    /**
     * Class construct
     *
     * @param EntityManager        $manager
     * @param FormFactoryInterface $formFactory
     * @param                      $applicationManager
     */
    public function __construct(
        EntityManager $manager,
        FormFactoryInterface $formFactory,
        $applicationManager
    ) {
        $this->manager = $manager;
        $this->formFactory = $formFactory;
        $this->applicationManager = $applicationManager;
    }

    /**
     * Post new mail
     *
     * @param       $appkey
     * @param array $parameters
     * @return Mail
     * @throws InvalidFormException
     */
    public function post($appkey, array $parameters)
    {
        $application = $this->applicationManager->findOneOr404(['apiKey' => $appkey]);

        $mail = new Mail();
        $mail->setApplication($application);
        $form = $this->formFactory->create(new MailType(), $mail);
        $form->submit($parameters);

        if ($form->isValid()) {
            $this->manager->persist($mail);
            $this->manager->flush();

            return $mail;
        } else {
            throw new InvalidFormException($form);
        }
    }
}