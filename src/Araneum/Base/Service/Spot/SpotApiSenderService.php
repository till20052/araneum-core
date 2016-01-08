<?php

namespace Araneum\Base\Service\Spot;

use Guzzle\Http\Message\Response;
use Guzzle\Service\ClientInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Application;
use Araneum\Bundle\AgentBundle\Entity\SpotLog;
use Guzzle\Http\Exception\BadResponseException;

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
     * @param boolean         $enableJsonResponse
     */
    public function __construct(
        ClientInterface $guzzle,
        EntityManager $em,
        $enableJsonResponse
    )
    {
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
    /**
     * Send request to core
     *
     * @param array $requestData
     * @param array $spotCredential
     * @return \Guzzle\Http\Message\Response
     */
    public function send(array $requestData, array $spotCredential)
    {
        if (!$this->isSpotCredentialValid($spotCredential)) {
            $error = "Check spot credential data, some value invalid: ".print_r($spotCredential, true);
            $this->createSpotLog([$requestData, $error], 400);
            throw new \BadMethodCallException($error);
        }

        $this->guzzle->setBaseUrl($spotCredential['url']);

        try {
            $response = $this->guzzle->post(
                null,
                null,
                array_merge(
                    [
                        'api_username' => $spotCredential['userName'],
                        'api_password' => $spotCredential['password'],
                        'jsonResponse' => $this->enableJsonResponse ? 'true' : 'false',
                    ],
                    $requestData
                )
            )->send();
            $code = $response->getStatusCode();
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $code = $e->getCode();
        } catch (\Exception $e) {
            $response = new Response($e->getCode());
            $response->setBody($e->getMessage());
            $code = $response->getStatusCode();
        }

        $this->createSpotLog([$requestData, $response->getBody()], $code);
        return $response;
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
        $spotLog = (new SpotLog())
            ->setStatus($status)
            ->setRequest($log['request'])
            ->setResponse($log['response'])
        ;

        $this->em->persist($spotLog);
        $this->em->flush();
    }
}