<?php

namespace Araneum\Base\Controller;

use Araneum\Base\Traits\TranslatorAwareTrait;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpKernel\Exception\FlattenException;

/**
 * Class CustomExceptionController
 *
 * @package Araneum\Base\Controller
 */
class CustomExceptionController extends ExceptionController
{
    use TranslatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $showException = $request->attributes->get('showException', $this->debug); // As opposed to an additional parameter, this maintains BC
        $code = $exception->getStatusCode();
        $translateAlias = $code.'.status_text';
        $statusText = $this->translator->trans($translateAlias, [], 'exceptions');
        if ($translateAlias == $statusText) {
            $statusText = isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '';
        }

        $translateAlias = $code.'.exception_message';
        $exceptionMessage = $this->translator->trans($translateAlias, [], 'exceptions');
        if ($translateAlias == $exceptionMessage) {
            $exceptionMessage = $exception->getMessage();
        }

        return new Response($this->twig->render(
            (string) $this->findTemplate($request, $request->getRequestFormat(), $code, $showException),
            array(
                'status_code'       => $code,
                'status_text'       => $statusText,
                'exception_message' => $exceptionMessage,
                'logger'            => $logger,
                'currentContent'    => $currentContent,
            )
        ));
    }
}
