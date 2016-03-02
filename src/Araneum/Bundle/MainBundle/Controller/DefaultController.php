<?php

namespace Araneum\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Admin panel action
     *
     * @return Response
     */
    public function adminAction()
    {
        return $this->render('admin.layout.html.twig');
    }

    /**
     * Render custom exception messages if they are exists
     *
     * @param string $code Exception code
     * @param string $text Exception text
     * @param string $message Exception message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exceptionCustomMessagesAction($code, $text, $message)
    {
        $translateAlias = $code.'.status_text';
        $translator = $this->get('translator');
        $statusText = $translator->trans($translateAlias, [], 'exceptions');
        if ($translateAlias == $statusText) {
            $statusText = $text;
        }

        $translateAlias = $code.'.exception_message';
        $exceptionMessage = $translator->trans($translateAlias, [], 'exceptions');
        if ($translateAlias == $exceptionMessage) {
            $exceptionMessage = $message;
        }

        return $this->render(
            'TwigBundle:Exception:error.details.html.twig',
            [
                'status_code' => $code,
                'status_text' => $statusText,
                'exception_message' => $exceptionMessage,
            ]
        );
    }
}
