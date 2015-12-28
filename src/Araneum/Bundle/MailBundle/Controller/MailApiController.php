<?php

namespace Araneum\Bundle\MailBundle\Controller;

use Araneum\Base\Exception\InvalidFormException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MailApiController
 *
 * @package Araneum\Bundle\MailBundle\Controller
 */
class MailApiController extends FOSRestController
{
    /**
     * Post Mail
     *
     * @ApiDoc(
     *   resource = "Mail",
     *   section = "MailBundle",
     *   description = "Save new mail",
     *   input = {
     *      "class" = "Araneum\Bundle\MailBundle\Form\Api\MailType",
     *      "name" = ""
     *   },
     *   statusCodes = {
     *      201 = "Returned when create",
     *      400 = "Returned when validation failed",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Application not found"
     *   },
     *   tags={"Mail"},
     *  parameters={
     *      {"name"="appKey", "dataType"="string", "required"=true, "description"="Application key"}
     *   }
     * )
     * @Rest\Post("api/mail",     name="araneum_mail_api_mail_post", defaults={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param  Request $request
     * @return array|View
     */
    public function postMailAction(Request $request)
    {
        try {
            $mail = $this->container
                ->get('araneum.mail.mail.api_handler')
                ->post($request->query->get('appKey'), $request->request->all());

            return ['id' => $mail->getId()];
        } catch (InvalidFormException $e) {

            return View::create($e->getForm(), 400);
        }
    }
}
