<?php

namespace Araneum\Bundle\UserBundle\Controller;

use Araneum\Base\Service\FormHandlerService;
use Araneum\Bundle\UserBundle\Entity\User;
use Araneum\Bundle\UserBundle\Form\Type\ProfileType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Araneum\Bundle\UserBundle\Service\DataTable\UserDataTableList;
use Araneum\Bundle\UserBundle\Service\Actions\UserActions;

/**
 * Class AdminUserController
 *
 * @package Araneum\Bundle\UserBundle\Controller
 */
class AdminProfileController extends Controller
{
    /**
     * Edit profile
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/profile/edit", name="araneum_user_adminUser_edit")
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $form = $this->createForm(new ProfileType(), $user);

        /**
         * @var FormHandlerService $formHandler
         */
        $formHandler = $this->get('araneum.base.form.handler');

        if ($request->getMethod() === 'POST') {
            $form->submit($request);

            if (!$form->isValid()) {
                return new JsonResponse(
                    ['errors' => $formHandler->getErrorMessages($form)],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $em->persist($user);
            $em->flush();

            return new JsonResponse(
                [
                    'username' => $user->getUsername(),
                    'fullName' => $user->getFullName(),
                    'email' => $user->getEmail(),
                ],
                Response::HTTP_ACCEPTED
            );
        }

        return new JsonResponse(
            ['form' => $formHandler->extract($form->createView())],
            Response::HTTP_OK
        );
    }

    /**
     * Get Authorized User Data
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *     "/profile/get_authorized_user_data",
     *     name="araneum_user_adminUser_getAuthorizedUserData"
     * )
     * @return JsonResponse
     */
    public function getAuthorizedUserData()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        return new JsonResponse(
            [
                'name' => $user->getFullName(),
                'email' => $user->getEmail(),
                'settings' => $user->getSettings(),
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Set user settings
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *     "/profile/settings",
     *     name="araneum_user_adminUser_setSettings"
     * )
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse $response
     */
    public function setSettingsAction(Request $request)
    {
        /**
         * @var EntityManager $entityManager
         */
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $this->getUser()
                ->setSettings($request->request->all());

            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return (new JsonResponse())
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }
}
