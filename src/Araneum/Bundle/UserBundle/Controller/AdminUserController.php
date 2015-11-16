<?php

namespace Araneum\Bundle\UserBundle\Controller;

use Araneum\Bundle\UserBundle\Entity\User;
use Araneum\Bundle\UserBundle\Form\Type\ProfileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminUserController extends Controller
{
    /**
     * Convert children of FormView to Array
     *
     * @param FormView|array $children
     * @param array $fields
     * @return array
     */
    private function extract($children, array $fields = ['name', 'full_name', 'label', 'value'])
    {
        $list = [];

        if ($children instanceof FormView) {
            $children = $children->children;
        }

        foreach ($children as $name => $child) {
            if (!(count($child->children) > 0)) {
                $item = [];

                foreach ($fields as $field) {
                    if (!isset($child->vars[$field])) {
                        continue;
                    }

                    $item[$field] = $child->vars[$field];
                }

                $list[] = $item;
            } else {
                $list = $list + $this->extract($child->children, $fields);
            }
        }

        return $list;
    }

    /**
     * Get Form Errors
     *
     * @param Form $form
     * @return array
     */
    private function getErrorMessages(Form $form)
    {
        $errors = [];

        foreach ($form->getErrors(true, true) as $error) {
            $message = $error->getMessage();

            if(in_array($message, $errors)){
                continue;
            }

            $errors[] = $message;
        }

        return $errors;
    }

    public function activateUserAction()
    {
        // TODO: add you implementation here
    }

    public function recoverPasswordAction()
    {
        // TODO: add you implementation here
    }

    /**
     * @Route("/profile/get_authorized_user_data", name="araneum_user_adminUser_getAuthorizedUserData")
     * @Security("has_role('ROLE_ADMIN')")
     * @return Response
     */
    public function getAuthorizedUserData()
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse(
            [
                'name' => $user->getFullName(),
                'email' => $user->getEmail(),
				'picture' => '/assets/build/img/user/no-image.jpg'
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Edit profile
     *
     * @Route("/profile/edit", name="araneum_user_adminUser_edit")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(new ProfileType(), $user);

        if ($request->getMethod() === 'POST') {
            $form->submit($request);

            if (!$form->isValid()) {
                return new JsonResponse(
                    [
                        'errors' => $this->getErrorMessages($form)
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $em->persist($user);
            $em->flush();

            return new JsonResponse(
                [
                    'username' => $user->getUsername(),
                    'fullName' => $user->getFullName(),
                    'email' => $user->getEmail()
                ],
                Response::HTTP_ACCEPTED
            );
        }

        return new JsonResponse(
            [
                'form' => $this->extract($form->createView())
            ],
            Response::HTTP_OK
        );
    }
}