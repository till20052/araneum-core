<?php
namespace Araneum\Bundle\MailBundle\Controller;

use Araneum\Bundle\MailBundle\Entity\Mail;
use Araneum\Bundle\MailBundle\Service\Actions\MailActions;
use Araneum\Bundle\MailBundle\Service\DataTable\MailDataTableList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminMailController
 *
 * @package Araneum\Bundle\MailBundle\Controller
 */
class AdminMailController extends Controller
{
    /**
     * Mail module initialization
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/mails/init.json", name="araneum_manage_mails_init")
     * @return JsonResponse
     */
    public function initAction()
    {
        $initializer = $this->get('araneum.admin.initializer.service');
        $filter = $this->get('araneum.mail.mail.filter.form');
        $code = JsonResponse::HTTP_OK;

        try {
            $initializer->setFilters($filter);
            $initializer->setGrid(
                new MailDataTableList($this->container),
                $this->generateUrl('araneum_manage_mails_grid')
            );
            $initializer->setActions(new MailActions());
        } catch (\Exception $exception) {
            $code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
            $initializer->setError($exception);
        }

        return new JsonResponse($initializer->get(), $code);
    }

    /**
     * Server/client datatable communication
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/mails/datatable.json", name="araneum_manage_mails_grid")
     * @return JsonResponse
     */
    public function datatableAction()
    {
        return $this
            ->get('araneum_datatable.factory')
            ->create(new MailDataTableList($this->container))
            ->execute();
    }

    /**
     * Get Mail by id
     *
     * @ApiDoc(
     *  resource = "Mail",
     *  section = "MailBundle",
     *  description = "Get mail",
     *  requirements={
     *      {"name"="_format", "dataType"="json", "description"="Output format must be json"}
     *  },
     *  parameters={
     *      {"name"="id", "dataType"="int", "required"=true, "description"="Id"},
     *  },
     *  statusCodes = {
     *      200 = "Returned on success",
     *      400 = "Returned when validation failed",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Application or Customer not found by defined condition"
     *  },
     *  tags={"Agent"}
     * )
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *      "/mails/mail/{id}",
     *      name="araneum_mail_admin_mail_get",
     *      requirements={"id" = "\d+"},
     *      defaults={"id" = null}
     * )
     * @Method("GET")
     * @param int $id
     * @return JsonResponse
     */
    public function getMailJsonAction($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository('AraneumMailBundle:Mail');

        $mail = $repository->findOneById($id);
        if (empty($mail)) {
            $mail = new Mail();
        }

        try {
            return new JsonResponse(
                $this
                    ->get('araneum.form_exporter.service')
                    ->get(
                        $this->get('araneum.mail.mail.form'),
                        $mail
                    ),
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                $exception->getMessage(),
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Delete mails one or many
     *
     * @ApiDoc(
     *  resource = "Mail",
     *  section = "MailBundle",
     *  description = "Delete mails",
     *  requirements={
     *      {"name"="_format", "dataType"="json", "description"="Output format must be json"}
     *  },
     *  parameters={
     *      {"name"="data", "dataType"="collection", "required"=true, "description"="array[id]"},
     *  },
     *  statusCodes = {
     *      202 = "Returned when delete was successful",
     *      400 = "Returned when validation failed",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Application or Customer not found by defined condition"
     *  },
     *  tags={"Agent"}
     * )
     *
     * @Route(
     *     "/mails/mail/delete",
     *     defaults={"_format"="json"},
     *     name="araneum_mail_admin_mail_delete"
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $idx = (array) $request->get('data');
        $mailRepository = $this->getDoctrine()->getRepository('AraneumMailBundle:Mail');

        if (count($idx) > 0) {
            $mailRepository->delete($idx);
        }

        return new JsonResponse('Success');
    }
}
