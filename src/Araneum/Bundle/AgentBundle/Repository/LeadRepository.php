<?php

namespace Araneum\Bundle\AgentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * LeadRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LeadRepository extends EntityRepository
{
	/**
	 * Find list of leads by email and/or phone as optionality
	 *
	 * @param array $filters
	 * @return array
	 */
	public function findByFilter($filters = [])
	{
		$queryBuilder = $this->createQueryBuilder('l');

		if (isset($filters['email'])) {
			if( ! preg_match('/[\w\d\.\-\@]{3,}/', $filters['email'])){
				throw new InvalidParameterException();
			}

			$queryBuilder->where('l.email LIKE :email')
				->setParameter('email', $filters['email'].'%');
		}

		if (isset($filters['phone'])) {
			if( ! preg_match('/[0-9\-\(\)]{3,17}/', $filters['phone'])){
				throw new InvalidParameterException();
			}

			$queryBuilder->andWhere('l.phone LIKE :phone')
				->setParameter('phone', $filters['phone'].'%');
		}

		return $queryBuilder->getQuery()->getResult();
	}
}
