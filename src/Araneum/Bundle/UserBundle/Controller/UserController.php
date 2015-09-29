<?php

namespace Araneum\Bundle\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Araneum\Bundle\UserBundle\Entity\User;
use Araneum\Bundle\UserBundle\Form\Type\ProfileType;

class UserController extends Controller
{
    /**
     * Shows profile form
     *
     * @Route("profile/", name="araneum_user_user_profileShow")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return Response
     */
    public function profileShowAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $form = $this->createForm(new ProfileType(), $this->getUser());

        if ($request->getMethod() === 'POST') {
            $form->submit($request);

            if ($form->isValid()) {
                $em->persist($this->getUser());
                $em->flush();

                $this->addFlash('notice', $this->get('translator')->trans('flash_edit_success'));

                return $this->render(
                    'AraneumUserBundle:Profile:show.html.twig',
                    [
                        'form' => $form->createView(),
                        'admin_pool' => $this->get('sonata.admin.pool')
                    ]
                );
            }
        }

        return $this->render(
            'AraneumUserBundle:Profile:show.html.twig',
            [
                'form' => $form->createView(),
                'admin_pool' => $this->get('sonata.admin.pool')
            ]
        );
    }
}