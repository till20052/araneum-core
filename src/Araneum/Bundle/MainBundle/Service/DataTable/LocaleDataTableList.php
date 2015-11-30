<?php

namespace Araneum\Bundle\MainBundle\Service\DataTable;

use Araneum\Base\Ali\DatatableBundle\Builder\AbstractList;
use Araneum\Base\Ali\DatatableBundle\Builder\ListBuilderInterface;
use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Repository\LocaleRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;

class LocaleDataTableList extends AbstractList
{
	/**
	 * Build the list
	 *
	 * @param ListBuilderInterface $builder
	 * @return null
	 */
	public function buildList(ListBuilderInterface $builder)
	{
		$builder
			->add('id')
			->add('name', ['search_type' => 'like'])
			->add('locale', ['search_type' => 'like'])
			->add('enabled')
			->add('orientation', ['render' => function ($value, $data, $doctrine, $templating, $user) {
				return $value != Locale::ORIENT_RGT_TO_LFT ? 'Left to right' : 'Right to left';
			}])
			->add('encoding', ['search_type' => 'like']);
	}

	/**
	 * Returns the name of entity class.
	 *
	 * @return string
	 */
	public function getEntityClass()
	{
		return 'AraneumMainBundle:Locale';
	}

	/**
	 * Create query builder
	 *
	 * @param Registry $doctrine
	 * @param          $user
	 * @return \Ali\DatatableBundle\Util\Factory\Query\QueryInterface
	 */
	public function createQueryBuilder(Registry $doctrine, $user = null)
	{
		/** @var LocaleRepository $repository */
		$repository = $doctrine->getRepository($this->getEntityClass());

		return $repository->getQueryBuilder();
	}
}