<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Exception\InvalidFormException;
use Araneum\Bundle\AgentBundle\Entity\Lead;
use Araneum\Bundle\AgentBundle\Form\Type\LeadType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;

class LeadApiHandlerService
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var FormFactory
	 */
	private $formFactory;

	/**
	 * Check incoming data
	 *
	 * @param array $data
	 * @return Lead
	 *
	 * @throws InvalidFormException
	 */
	private function verifyDataByForm($data)
	{
		$lead = new Lead();
		$form = $this->formFactory
			->create(new LeadType(), $lead)
			->submit($data);

		if (!$form->isValid()) {
			throw new InvalidFormException($form, 'Not valid data');
		}

		return $lead;
	}

	/**
	 * Service constructor
	 *
	 * @param EntityManager $entityManager
	 * @param FormFactory $formFactory
	 */
	public function __construct(EntityManager $entityManager, FormFactory $formFactory)
	{
		$this->entityManager = $entityManager;
		$this->formFactory = $formFactory;
	}

	/**
	 * Find list of leads by email and/or phone as optionality
	 *
	 * @param array $filters
	 * @return array
	 */
	public function find($filters = [])
	{
		return $this->entityManager
			->getRepository('AraneumAgentBundle:Lead')
			->findByFilter($filters);
	}

	/**
	 * Create lead
	 *
	 * @param array $data
	 * @return Lead
	 */
	public function create($data)
	{
		$lead = $this->verifyDataByForm($data);

		$this->entityManager->persist($lead);
		$this->entityManager->flush();

		return $lead;
	}
}