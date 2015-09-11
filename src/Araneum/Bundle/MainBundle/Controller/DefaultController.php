<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Validator\Constraints\Email;

class DefaultController extends Controller
{
    /**
     * Main method
     *
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return [
            'name' => 'test'
        ];
    }
}
