<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * Index action of Default controller
     *
     * @Route("/")
     *
     * @return Response
     */
    public function indexAction()
    {
        return new Response();
    }

    /**
     * @Route("/getMenu")
     * @return Response
     */
    public function getMenuFromConfigAction()
    {

        $yaml = new Parser();

        $array = $yaml->parse(file_get_contents('/../Resources/menu/left.yml'));

        $array = json_encode($array);

        $response = new Response();
        $response->setContent($array);

        return $response;
    }
}
