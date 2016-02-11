<?php
namespace Araneum\Bundle\MainBundle\Controller;

use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Service\Actions\ApplicationActions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\All;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class AdminApplicationController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class AdminApplicationController extends Controller
{

    /**
     * Save applications
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *     "/applications/application/save",
     *     name="araneum_applications_admin_applicarion_post"
     * )
     * @Method("POST")
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function saveApplicationPostAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AraneumMainBundle:Application');

        try {
            if (!empty($id)) {
                $application = $repository->findOneById($id);
                $code = JsonResponse::HTTP_ACCEPTED;
            } else {
                $application = new Application();
                $code = JsonResponse::HTTP_CREATED;
            }

            $form = $this->createForm($this->get('araneum.main.application.form'), $application);
            $form->submit($request->request->all());

            if ($form->isValid()) {
                $em->persist($application);
                $em->flush();

                return new JsonResponse(
                    [
                        'message' => 'Locale has been saved',
                        'id' => $application->getId(),
                    ],
                    $code
                );
            } else {

                return new JsonResponse(
                    ['message' => (string) $form->getErrors(true, false)],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }
        } catch (\Exception $exception) {

            return new JsonResponse(
                ['message' => $exception->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Enable applications chceck status
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *     "/manage/applications/application/status",
     *     name="araneum_applications_admin_application_status"
     * )
     * @Method("POST")
     * @param  Request $request
     * @return Response
     */
    public function checkStatusAction(Request $request)
    {
        return $this->runCheckStatusAction($request);
    }

    /**
     * Enable applications one or many
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *     "/manage/applications/application/enable",
     *     name="araneum_main_admin_application_enable"
     * )
     * @Method("POST")
     * @param  Request $request
     * @return Response
     */
    public function enableAction(Request $request)
    {
        return $this->updateApplicationEnableDisableAction($request, true);
    }

    /**
     * Disable applications one or many
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *     "/manage/applications/application/disable",
     *     name="araneum_main_admin_application_disable"
     * )
     * @param  Request $request
     * @return Response
     */
    public function disableAction(Request $request)
    {
        return $this->updateApplicationEnableDisableAction($request, false);
    }

    /**
     * Check application status
     *
     * @param  Request $request
     * @return JsonResponse
     */
    private function runCheckStatusAction(Request $request)
    {
        $idx = $request->request->get('data');
        $serviceApplicationCheck = $this->container
            ->get('araneum.main.application.checker');

        if (!is_array($idx)) {
            return new JsonResponse('Data must be an array');
        }

        $errors = $this->get('validator')->validate($idx, new All([new Regex('/^\d+$/')]));
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors);
        }

        $error = [];
        foreach ($idx as $id) {
            $status = $serviceApplicationCheck->checkApplication($id);
            if ($status != Application::STATUS_OK) {
                $error[$id] = Application::getStatusDescription($status);
            }
        }

        return (count($error) > 0) ? new JsonResponse($error) : new JsonResponse('Success');
    }

    /**
     * Update application state
     *
     * @param  Request $request
     * @param  bool    $state
     * @return JsonResponse
     */
    private function updateApplicationEnableDisableAction(Request $request, $state)
    {
        $idx = $request->request->get('data');

        $applicationRepository = $this->getDoctrine()->getRepository('AraneumMainBundle:Application');

        if (!is_array($idx)) {
            return new JsonResponse('Data must be an array');
        }

        $errors = $this->get('validator')->validate($idx, new All([new Regex('/^\d+$/')]));
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors);
        }

        $applicationRepository->updateEnabled($idx, $state);

        return new JsonResponse('Success');
    }
}
