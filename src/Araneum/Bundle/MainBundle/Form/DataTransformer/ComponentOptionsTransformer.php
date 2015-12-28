<?php

namespace Araneum\Bundle\MainBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ComponentOptionsTransformer
 *
 * @package Araneum\Bundle\MainBundle\Form\DataTransformer
 */
class ComponentOptionsTransformer implements DataTransformerInterface
{
    /**
     * Convert data from db format to form format
     *
     * @param  array|ArrayCollection $value
     * @return void|ArrayCollection
     */
    public function transform($value)
    {
        if (!$value instanceof ArrayCollection) {
            return;
        }

        return $value;
    }

    /**
     * Convert data from form format to db format
     *
     * @param  ArrayCollection $value
     * @return array
     */
    public function reverseTransform($value)
    {
        $options = [];

        foreach ($value as $option) {
            $options[$option['key']] = $option['value'];
        }

        return $options;
    }
}
