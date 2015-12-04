<?php

namespace Araneum\Bundle\UserBundle\Service\DataTable;

use Araneum\Base\Ali\DatatableBundle\Builder\AbstractList;
use Araneum\Base\Ali\DatatableBundle\Builder\ListBuilderInterface;
use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Repository\LocaleRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;

class UserDataTableList extends AbstractList
{
	/**
	 * Query Builder
	 *
	 * @var
	 */
	private $queryBuilder;

	/**
	 * Container
	 *
	 * @var
	 */
	private $container;

	/**
	 * UserDatatableList constructor.
	 *
	 * @param $container
	 */
	public function __construct($container)
	{
		$this->container = $container;
	}

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
			->add('email', ['search_type' => 'like'])
			->add('fullname', ['search_type' => 'like'])
			->add('enabled')
			->add('role', ['render' => function ($value, $data, $doctrine, $templating, $user) {
				return $value;
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
		return 'AraneumUserBundle:User';
	}

	/**
	 * Create query builder
	 *
	 * @param $doctrine
	 * @return \Ali\DatatableBundle\Util\Factory\Query\QueryInterface
	 */
	public function createQueryBuilder($doctrine)
	{
		/** @var UserRepository $repository */
		$repository = $doctrine->getRepository($this->getEntityClass());
		if(empty($this->queryBuilder)) {
			$this->queryBuilder = $repository->getQueryBuilder();

			$filters = $this->container->get('form.factory')->create(
				$this->container->get('araneum_user.user.filter.form')
			);

			if ($this->container->get('request')->query->has($filters->getName())) {
				$filters->submit($this->container->get('request')->query->get($filters->getName()));
				$this->container->get('lexik_form_filter.query_builder_updater')->addFilterConditions(
						$filters,
						$this->queryBuilder
				);
			}
		}

		return $this->queryBuilder;
	}
}