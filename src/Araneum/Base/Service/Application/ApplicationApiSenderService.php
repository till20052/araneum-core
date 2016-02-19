<?php

namespace Araneum\Base\Service\Application;

use Araneum\Base\Service\AbstractApiSender;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class ApplicationApiSenderService
 *
 * @package Araneum\Base\Service\Application
 */
class ApplicationApiSenderService extends AbstractApiSender
{

    /**
     * Send request to application
     *
     * @param  array $requestData
     * @param  array $helper
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function prepareToSend($requestData, $helper)
    {
        $url = $helper['url'];
        $id = $helper['customerId'];
        $this->guzzle->setBaseUrl($url);
        $response = $this->guzzle->post(null, null, $requestData)->send();
        $data = $response->getBody();
        if (isset($data['id'])) {
            $this->updateCustomerSiteId($id, $data['id']);
        } else {
            throw new Exception('Cannot get valid answer from Application');
        }

        return $response;
    }

    /**
     * Create and save customer log
     *
     * @param int     $id
     * @param int     $siteId
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateCustomerSiteId($id, $siteId)
    {
        $customer = $this->em->getRepository('AraneumAgentBundle:Customer')->find($id);
        if (!empty($customer)) {
            $customer->setSiteId($siteId);
            $this->em->persist($customer);
            $this->em->flush();
        } else {
            throw new Exception('Customer with id '.$id.' not found');
        }
    }
}
