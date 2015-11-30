<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LocaleRepository
 */
class LocaleRepository extends EntityRepository
{
	/**
	 * Return Locale Query Builder without any conditions
	 *
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilder()
	{
		return $this->createQueryBuilder('l');
	}
}