<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Araneum\Base\Repository\CountableTrait;
use Araneum\Base\Repository\AdminDataGridTrait;
use Doctrine\ORM\EntityRepository;

/**
 * LocaleRepository
 */
class LocaleRepository extends EntityRepository implements \Countable
{
    use CountableTrait;

    use AdminDataGridTrait;
}
