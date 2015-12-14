<?php

namespace Araneum\Base\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use JMS\Serializer\SerializerInterface;

/**
 * Class FromExporterService
 *
 * @package Araneum\Base\Service
 */
class FromExporterService
{
    private $serializer;

    private $factory;

    /**
     * @param SerializerInterface  $serializer
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(SerializerInterface $serializer, FormFactoryInterface $formFactory)
    {
        $this->serializer = $serializer;
        $this->factory = $formFactory;
    }

    /**
     * Export form to array
     *
     * @param string|FormTypeInterface $form The type of the form
     * @param array                    $data
     * @return mixed
     */
    public function get($form, $data = [])
    {
        return $this->serializer->toArray(
            $this->factory->create($form, $data)->createView()
        );
    }
}
