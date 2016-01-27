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
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class AdminUserController
 *
 * @package Araneum\Bundle\UserBundle\Controller
 */
class AdminUserController extends Controller
{

    /**
     *
     */
    public function recoverPasswordAction()
    {
        // TODO: add you implementation here
    }

    /**
     * Get locale by id
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *      "/users/user/{id}",
     *      name="araneum_user_admin_user_get",
     *      requirements={"id" = "\d+"},
     *      defaults={"id" = null}
     * )
     * @Method("GET")
     * @param         int $id
     * @return        JsonResponse
     */
    public function getUserJsonAction($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository('AraneumUserBundle:User');

        $user = $repository->findOneById($id);

        if (empty($user)) {
            $user = new User();
        };

        try {
            return new JsonResponse(
                $this
                    ->get('araneum.form_exporter.service')
                    ->get(
                        $this->get('araneum_user.user.form'),
                        $user
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
     * Save user
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(
     *     "/users/user/save",
     *     name="araneum_user_admin_user_post"
     * )
     * @Method("POST")
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function saveUserPostAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AraneumUserBundle:User');
        try {
            if (!empty($id)) {
                $user = $repository->findOneById($id);
                $code = JsonResponse::HTTP_ACCEPTED;
            } else {
                $user = new User();
                if (empty($request->get('plainPassword'))) {
                    throw new Exception("Password should not be Blank");
                }
                $code = JsonResponse::HTTP_CREATED;
            }

            $form = $this->createForm($this->get('araneum_user.user.form'), $user);
            $form->submit($request->request->all());

            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();

                return new JsonResponse(
                    [
                        'message' => 'User has been saved',
                        'id' => $user->getId(),
                    ],
                    $code
                );
            } else {
                $errorList = $this->get('validator')->validate($user);
                $msg = "";
                foreach ($errorList as $err) {
                    $msg .= $this->get('translator')->trans($err->getMessage()).". ";
                }

                return new JsonResponse(
                    ['message' => $msg],
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
     * Enable users one or many
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/users/user/enable", name="araneum_user_admin_user_enable")
     * @Method("POST")
     * @param          Request $request
     * @return         Response
     */
    public function enableAction(Request $request)
    {
        return $this->updateUserEnableDisableAction($request, true);
    }

    /**
     * Disable users one or many
     *
     * @param      Request $request
     * @return     Response
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/users/user/disable", name="araneum_user_admin_user_disable")
     */
    public function disableAction(Request $request)
    {
        return $this->updateUserEnableDisableAction($request, false);
    }

    /**
     * Delete users one or many
     *
     * @Route("/users/user/delete", defaults={"_format"="json"}, name="araneum_user_admin_user_delete")
     * @param      Request $request
     * @return     JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $idx = $request->request->get('data');
        $userRepository = $this->getDoctrine()->getRepository('AraneumUserBundle:User');

        if (is_array($idx) && count($idx) > 0) {
            $userRepository->delete($idx);
        }

        return new JsonResponse('Success');
    }

    /**
     * Locales module initialization
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/users/init.json", name="araneum_manage_users_init")
     * @return JsonResponse
     */
    public function initAction()
    {
        $initializer = $this->get('araneum.admin.initializer.service');
        $filter = $this->get('araneum_user.user.filter.form');
        $code = JsonResponse::HTTP_OK;

        try {
            $initializer->setFilters($filter);
            $initializer->setGrid(
                new UserDataTableList($this->container),
                $this->generateUrl('araneum_manage_users_grid')
            );
            $initializer->setActions(new UserActions());
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
     * @Route("/users/datatable.json", name="araneum_manage_users_grid")
     * @return JsonResponse
     */
    public function datatableAction()
    {
        return $this
            ->get('araneum_datatable.factory')
            ->create(new UserDataTableList($this->container))
            ->execute();
    }

    /**
     * Update user state
     *
     * @param  Request $request
     * @param  bool    $state
     * @return JsonResponse
     */
    private function updateUserEnableDisableAction(Request $request, $state)
    {
        $idx = $request->request->get('data');

        $userRepository = $this->getDoctrine()->getRepository('AraneumUserBundle:User');

        if (!is_array($idx)) {
            return new JsonResponse('Data must be an array');
        }

        $errors = $this->get('validator')->validate($idx, new All([new Regex('/^\d+$/')]));
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors);
        }

        $userRepository->updateEnabled($idx, $state);

        return new JsonResponse('Success');
    }
}
