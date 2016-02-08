<?php

namespace Araneum\Base\Service;

use Guzzle\Http\Message\Response;
use Guzzle\Service\ClientInterface;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Exception\CurlException;
use Araneum\Bundle\AgentBundle\Entity\SenderLog;

abstract class AbstractApiSender
{
    /**
     * @var ClientInterface
     */
    protected $guzzle;
    protected $enableJsonResponse;

    /**
     * Abstract sender constructor.
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
     * Send request to core
     *
     * @param array $data
     * @param array $credential
     * @return \Guzzle\Http\Message\Response
     */

    public function send(array $data, array $credential)
    {
        $log = array('request' => $data);
        try{
            $response = $this->prepareToSend($data, $credential);
            if (!empty($response)) {
                $log['response'] = $response->getBody(true);
            }
            $this->createSenderLog($log, SenderLog::TYPE_OK);

            return $response;

        } catch (\BadMethodCallException $e) {
            $log['response'] = $e->getMessage();
            $this->createSenderLog($log, SenderLog::TYPE_BAD_METHOD_CALL);

            return $e;
        } catch (CurlException $e) {
            $log['response'] = $e->getError();
            $this->createSenderLog($log, SenderLog::TYPE_CURL);

            return $e;
        } catch (RequestException $e) {
            $code = $e->getRequest()->getResponse()->getStatusCode();
            $message = $e->getRequest()->getResponse()->getBody(true);
            $log['response'] = $code.' : '.$message;
            $this->createSenderLog($log, SenderLog::TYPE_REQUEST);

            return $e;
        } catch (\Exception $e) {
            $log['response'] = $e->getCode().' : '.$e->getMessage();
            $this->createSenderLog($log, SenderLog::TYPE_OTHER_EXCEPTION);

            return $e;
        }
    }

    /**
     * Get errors from response or null if no errors
     *
     * @param Response $response
     * @return string|null
     */
    public function getErrors(Response $response)
    {
        $decodedResponse = $response->json();
        if (!array_key_exists('status', $decodedResponse)) {
            throw new \BadMethodCallException('Unsupported response format '.print_r($decodedResponse, true));
        }

        $status = $decodedResponse['status'];
        if (array_key_exists('connection_status', $status) &&
            $status['connection_status'] === 'successful' &&
            array_key_exists('operation_status', $status) &&
            $status['operation_status'] === 'successful'
        ) {
            return null;
        }

        return json_encode($status['errors']);
    }

    /**
     *
     * @param Response $response
     * @return string|null
     */
    public function getErrorsFromPublic(Response $response)
    {
        $decodedResponse = $response->json();
        if (!array_key_exists('status', $decodedResponse)) {
            throw new \BadMethodCallException('Unsupported response format '.print_r($decodedResponse, true));
        }

        if ($decodedResponse['status'] === true) {
            return null;
        }

        return json_encode($decodedResponse['errors']);
    }

    /**
     * Create and save spot log
     *
     * @param array  $log
     * @param int    $status
     * @throws \Doctrine\ORM\ORMException
     */
    private function createSenderLog(array $log, $status)
    {
        if (is_array($log['request'])) {
            $log['request'] = json_encode($log['request']);
        }
        if (is_array($log['response'])) {
            $log['response'] = json_encode($log['response']);
        }

        $entityLog = (new SenderLog())
            ->setStatus($status)
            ->setRequest($log['request'])
            ->setResponse($log['response'])
        ;

        $this->em->persist($entityLog);
        $this->em->flush();
    }

    /**
     * @param array     $data
     * @param array     $credential
     * @return Response $response
     */
    abstract public function prepareToSend($data, $credential);
}