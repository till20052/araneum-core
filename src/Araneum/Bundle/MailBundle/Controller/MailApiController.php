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
     *      {"name"="apiKey", "dataType"="string", "required"=true, "description"="apiKey"}
     *   }
     * )
     * @Route("api/v1/mail", name="araneum_mail_api_mail_post")
     * @Method({"POST"})
     * @Rest\View(statusCode=201)
     *
     * @return array
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