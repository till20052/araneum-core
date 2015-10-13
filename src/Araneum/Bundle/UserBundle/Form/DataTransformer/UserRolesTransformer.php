<?php

namespace Araneum\Bundle\UserBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserRolesTransformer implements DataTransformerInterface
{
	public function transform($value)
	{
		if( ! ($value instanceof ArrayCollection)){
			return;
		}

		return $value;
	}

	public function reverseTransform($roles)
	{
		$list = [];

		foreach($roles as $role)
		{
			$list[] = $role->getName();
		}

		return $list;
	}
}