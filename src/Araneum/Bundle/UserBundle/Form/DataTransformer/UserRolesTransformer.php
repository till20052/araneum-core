<?php

namespace Araneum\Bundle\UserBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class UserRolesTransformer
 *
 * @package Araneum\Bundle\UserBundle\Form\DataTransformer
 */
class UserRolesTransformer implements DataTransformerInterface
{
    /**
     * Transform roles
     *
     * @param  mixed $value
     * @return void|ArrayCollection
     */
    public function transform($value)
    {
        if (!($value instanceof ArrayCollection)) {
            return;
        }

        return $value;
    }

    /**
     * Reverse transform roles
     *
     * @param  mixed $roles
     * @return array
     */
    public function reverseTransform($roles)
    {
        $list = [];

        foreach ($roles as $role) {
            $list[] = $role->getName();
        }

        return $list;
    }
}
