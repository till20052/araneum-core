<?php
namespace Araneum\Bundle\UserBundle\Repository;

use Araneum\Base\Repository\CountableTrait;
use Araneum\Base\Repository\AdminDataGridTrait;

use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 *
 * @package Araneum\Bundle\UserBundle\Repository
 */
class UserRepository extends EntityRepository implements \Countable
{
    use CountableTrait;


    use AdminDataGridTrait;
}
