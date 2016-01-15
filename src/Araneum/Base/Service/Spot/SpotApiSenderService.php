<?php

namespace Araneum\Base\Service\Spot;

use Guzzle\Http\Message\Response;
use Guzzle\Service\ClientInterface;
use Doctrine\ORM\EntityManager;
use Araneum\Bundle\AgentBundle\Entity\SpotLog;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Exception\CurlException;

/**
 * Class SpotApiSenderService
 *
 * @package Araneum\Base\Service\Guzzle
 */
class SpotApiSenderService
{
    /**
     * @var ClientInterface
     */
    protected $guzzle;
    protected $enableJsonResponse;

    /**
     * SpotApiService constructor.
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
     * Send request to spot public api url
     *
     * @param string $method           HTTP method. Defaults to GET
     * @param string $spotApiPublicUrl
     * @param string $path
     * @param array  $requestData
     * @return \Guzzle\Http\Message\Response
     */
    public function sendToPublicUrl($method, $spotApiPublicUrl, $path, array $requestData)
    {
        if (!filter_var($spotApiPublicUrl, FILTER_VALIDATE_URL)) {
            throw new \BadMethodCallException("Not valid spot public utl: ".$spotApiPublicUrl);
        }

        $this->guzzle->setBaseUrl($spotApiPublicUrl);

        return $this->guzzle->createRequest($method, $path, null, $requestData)->send();
    }

    /**
     * Get data from spotoption
     *
     * @param array $requestData
     * @param array $spotCredential
     * @return array
     */
    public function get(array $requestData, array $spotCredential)
    {
        $response = $this->send($requestData, $spotCredential);
        $response = $response->json();

        if (isset($response['status']['connection_status']) &&
            $response['status']['connection_status'] === 'successful' &&
            $response['status']['operation_status'] === 'successful'
        ) {
            return $response['status'][$requestData['MODULE']];
        } else {
            return $response['status']['errors']['error'];
        }
    }

    /**
     * Send request to core
     *
     * @param array $requestData
     * @param array $spotCredential
     * @return \Guzzle\Http\Message\Response
     */
    public function send(array $requestData, array $spotCredential)
    {
        $log = array('request' => $requestData);
        try {
            if (!$this->isSpotCredentialValid($spotCredential)) {
                $error = "Check spot credential data, some value invalid: ".print_r($spotCredential, true);
                throw new \BadMethodCallException($error);
            }

            $this->guzzle->setBaseUrl($spotCredential['url']);
            $body = array_merge(
                [
                    'api_username' => $spotCredential['userName'],
                    'api_password' => $spotCredential['password'],
                    'jsonResponse' => $this->enableJsonResponse ? 'true' : 'false',
                ],
                $requestData
            );
            $log['response'] = $body;
            $response = $this->guzzle->post(null, null, $body)->send();
            if (!empty($response)) {
                $log['response'] = $response->getBody(true);
            }
            $this->createSpotLog($log, SpotLog::TYPE_OK);

            return $response;

        } catch (\BadMethodCallException $e) {
            $log['response'] = $e->getMessage();
            $this->createSpotLog($log, SpotLog::TYPE_BAD_METHOD_CALL);

            return $e;
        } catch (CurlException $e) {
            $log['response'] = $e->getError();
            $this->createSpotLog($log, SpotLog::TYPE_CURL);

            return $e;
        } catch (RequestException $e) {
            $code = $e->getRequest()->getResponse()->getStatusCode();
            $message = $e->getRequest()->getResponse()->getBody(true);
            $log['response'] = $code.' : '.$message;
            $this->createSpotLog($log, SpotLog::TYPE_REQUEST);

            return $e;
        } catch (\Exception $e) {
            $log['response'] = $e->getCode().' : '.$e->getMessage();
            $this->createSpotLog($log, SpotLog::TYPE_OTHER_EXCEPTION);

            return $e;
        }
    }

    /**
     * Get errors from Spot response or null if no errors
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
     * Get spotsession from cookies
     *
     * @param string $cookie
     * @return mixed
     */
    public function getSpotSessionFromPublic($cookie)
    {
        preg_match('/spotsession.{10,15}=(.{32}); /', $cookie, $matches);
        if (!array_key_exists(1, $matches)) {
            throw new \BadMethodCallException('Cookie must contains spotsession, cookie: '.$cookie);
        }

        return $matches[1];
    }

    /**
     * Validate spot credential
     *
     * @param array $spotCredential
     * @return bool
     */
    private function isSpotCredentialValid($spotCredential)
    {
        return
            array_key_exists('url', $spotCredential) &&
            array_key_exists('userName', $spotCredential) &&
            array_key_exists('password', $spotCredential) &&
            filter_var($spotCredential['url'], FILTER_VALIDATE_URL) &&
            $spotCredential['userName'] !== null &&
            $spotCredential['password'] !== null;
    }

    /**
     * Create and save spot log
     *
     * @param array  $log
     * @param int    $status
     * @throws \Doctrine\ORM\ORMException
     */
    private function createSpotLog(array $log, $status)
    {
        if (is_array($log['request'])) {
            $log['request'] = json_encode($log['request']);
        }
        if (is_array($log['response'])) {
            $log['response'] = json_encode($log['response']);
        }

        $spotLog = (new SpotLog())
            ->setStatus($status)
            ->setRequest($log['request'])
            ->setResponse($log['response'])
        ;

        $this->em->persist($spotLog);
        $this->em->flush();
    }
}
