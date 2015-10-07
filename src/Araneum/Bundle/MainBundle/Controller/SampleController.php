<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SampleController extends Controller
{
    /**
     * Main method
     *
     * @Route("/rabbitmq")
     */
    public function indexAction()
    {
        # get producer service
        $this
            ->get('old_sound_rabbit_mq.sample_producer')
            ->publish(serialize(['foo' => 'bar', '_FOO' => '_BAR']), 'rabbitmq');
    }
}
