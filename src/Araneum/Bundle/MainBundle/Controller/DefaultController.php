<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Araneum\Bundle\AgentBundle\DependencyInjection\AraneumAgentExtension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Araneum\Bundle\AgentBundle\DependencyInjection\Configuration;

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

        $array = [
            'Main' => [
                'one' => 'oneIOtem',
                'two' => 'secondItem'
            ]
        ];

        $array = json_encode($array);

        $response = new Response();
        $response->setContent($array);

        return $response;
    }
}
