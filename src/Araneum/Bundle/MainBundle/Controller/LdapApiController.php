<?php
namespace Araneum\Bundle\MainBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LdapApiController
 *
 * @package Araneum\Bundle\MainBundle\Controller
 */
class LdapApiController extends Controller
{
    /**
     * Go run Synchronization user LDAP
     *
     * @ApiDoc(
     *   resource = "Ldap",
     *   section = "MainBundle",
     *   description = "Gets all users in LDAP",
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
     *   tags={"LdapApi"}
     * )
     *
     * @Rest\Get(
     *      "/api/ldap/users/",
     *      name="araneum_main_api_ldap_users",
     *      defaults={"_format"="json"}
     * )
     * @Security("has_role('ROLE_API')")
     * @Rest\View()
     *
     * @return array
     */
    public function getLdapSynchronizationAction()
    {
        try {
            $serviceLdap = $this->container
                ->get('api.ldap.synchronization');
            $result = $serviceLdap->runSynchronization();

            return new JsonResponse($result, 200);
        } catch (\Exception $e) {

            return new JsonResponse("LDAP Error: ".$e->getMessage(), $e->getCode());
        }
    }
}
