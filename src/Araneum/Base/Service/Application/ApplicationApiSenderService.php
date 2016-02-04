<?php

namespace Araneum\Base\Service\Application;

use Guzzle\Http\Message\Response;
use Guzzle\Service\ClientInterface;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Exception\CurlException;
use Araneum\Bundle\AgentBundle\Entity\CustomerLog;

/**
 * Class ApplicationApiSenderService
 *
 * @package Araneum\Base\Service\Application
 */
class ApplicationApiSenderService
{
    /**
     * @var ClientInterface
     */
    protected $guzzle;
    protected $enableJsonResponse;

    /**
     * ApplicationApiSenderService constructor.
     *
     * @param ClientInterface $guzzle
     * @param EntityManager   $em
     * @param boolean         $enableJsonResponse
     */
    public function __construct(
        ClientInterface $guzzle,
        EntityManager $em,
        $enableJsonResponse
    ) {
        $this->guzzle = $guzzle;
        $this->em = $em;
        $this->enableJsonResponse = $enableJsonResponse;
    }

    /**
     * Send request to api url
     *
     * @param  string $method      HTTP method. GET
     * @param  string $apiUrl
     * @param  string $path
     * @param  array  $requestData
     * @return \Guzzle\Http\Message\Response
     */
    public function sendToUrl($method, $apiUrl, $path, array $requestData)
    {
        if (!filter_var($apiUrl, FILTER_VALIDATE_URL)) {
            throw new \BadMethodCallException("Not valid public url: ".$apiUrl);
        }

        $this->guzzle->setBaseUrl($apiUrl);

        return $this->guzzle->createRequest($method, $path, null, $requestData)->send();
    }

    /**
     * Get data from application
     *
     * @param  array  $requestData
     * @param  string $url
     * @return array
     */
    public function get(array $requestData, $url)
    {
        $response = $this->send($requestData, $url);
        $response = $response->json();

        return $response;
    }

    /**
     * Send request to application
     *
     * @param  array  $requestData
     * @param  array  $application
     * @return \Guzzle\Http\Message\Response
     */
    public function send(array $requestData, $application)
    {
        $log = array('request' => $requestData);
        $log['applicationId'] = $application['id'];
        $url = $application['url'];
        try {

            $this->guzzle->setBaseUrl($url);

            $log['response'] = $requestData;
            $response = $this->guzzle->post(null, null, $requestData)->send();
            if (!empty($response)) {
                $log['response'] = $response->getBody(true);
            }
            $this->createCustomerLog($log, CustomerLog::STATUS_OK);

            return $response;

        } catch (\BadMethodCallException $e) {
            $log['response'] = $e->getMessage();
            $this->createCustomerLog($log, CustomerLog::STATUS_ERROR);

            return $e;
        } catch (CurlException $e) {
            $log['response'] = $e->getError();
            $this->createCustomerLog($log, CustomerLog::STATUS_ERROR);

            return $e;
        } catch (RequestException $e) {
            $code = $e->getRequest()->getResponse()->getStatusCode();
            $message = $e->getRequest()->getResponse()->getBody(true);
            $log['response'] = $code.' : '.$message;
            $this->createCustomerLog($log, CustomerLog::STATUS_ERROR);

            return $e;
        } catch (\Exception $e) {
            $log['response'] = $e->getCode().' : '.$e->getMessage();
            $this->createCustomerLog($log, CustomerLog::STATUS_ERROR);

            return $e;
        }
    }

    /**
     * Create and save customer log
     *
     * @param array  $log
     * @param int    $status
     * @throws \Doctrine\ORM\ORMException
     */
    private function createCustomerLog(array $log, $status)
    {
        if (is_array($log['request'])) {
            $log['request'] = json_encode($log['request']);
        }
        if (is_array($log['response'])) {
            $log['response'] = json_encode($log['response']);
        }

        $customerLog = (new CustomerLog())
            ->setAction($log['action'])
            ->setApplication($this->em->getReference('AraneumMainBundle:Application', $log['applicationId']))
            ->setCustomer($this->em->getReference('AraneumAgentBundle:Customer', $log['id']))
            ->setResponse($log['response'])
            ->setStatus($status);

        $this->em->persist($customerLog);
        $this->em->flush();
    }
}
