<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * Main method
     *
     * @Route("/", name="test_test")
     * @Template()
     */
    public function indexAction()
    {
        $c = $this->getDoctrine()->getRepository('AraneumMainBundle:Connection')->findConnectionByAppKey(
            111111111111111
        );

        die(var_dump($c));
        return [
            'name' => 'test'
        ];
    }
}
