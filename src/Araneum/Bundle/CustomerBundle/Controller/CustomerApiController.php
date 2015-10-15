<?php

namespace Araneum\Bundle\CustomerBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Araneum\Bundle\CustomerBundle\Form\CustomerType;
use Araneum\Bundle\CustomerBundle\Entity\Customer;

class CustomerApiController extends FOSRestController
{

    /**
     * Get Application config by appKey
     *
     * @ApiDoc(
     *   resource = "Customer",
     *   section = "CustomerBundle",
     *   description = "Gets a Application config for a given key",
     *   output = "Araneum\Bundle\Customer\Entity\Application",
     *   statusCodes = {
     *      200 = "Returned when successful",
     *      403 = "Returned when authorization is failed",
     *      404 = "Returned when Application not found"
     *   },
     *   requirements = {
     *      {
     *          "name" = "_format",
     *          "dataType" = "json",
     *          "description" = "Output format must be json"
     *      }
     *   },
     *   parameters={
     *      {"name"="appKey", "dataType"="string", "required"=true, "description"="appKey"}
     *   },
     *   tags={"ApplicationApi"}
     * )
     *
     * @Route("/customers/data")
     * @Method({"POST"})
     *
     * @Rest\View(templateVar="customer")
     *
     * @param Request $request
     * @return mixed
     */
    public function setCustomerAction(Request $request)
    {
        $appKey = $request->query->get('appKey', $request);

        $postParameters = $request->request->all();

        $customer = new Customer();

        $form = $this->createForm(new CustomerType(), $customer);

        return $this->container
            ->get('araneum.customer.api.handler')
            ->getCustomer($appKey, $postParameters, $form, $customer);
    }

}