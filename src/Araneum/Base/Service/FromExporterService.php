<?php

namespace Araneum\Base\Service;


class FromExporterService
{
    private $serializer;

    private $factory;

    /**
     * FromExporter constructor.
     *
     * @param $serializer
     * @param $formFactory
     */
    public function __construct($serializer, $formFactory)
    {
        $this->serializer = $serializer;
        $this->factory = $formFactory;
    }

    /**
     * Export form to array
     *
     * @param $form
     * @param array $data
     * @return mixed
     */
    public function get($form, $data = [])
    {
        return $this->serializer->toArray(
            $this->factory->create($form, $data)->createView()
        );
    }
}