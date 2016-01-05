<?php

namespace Araneum\Base\Service\Spot;

use Guzzle\Http\Message\Response;
use Guzzle\Service\ClientInterface;
use Symfony\Component\Console\Application;

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
    public function __construct(ClientInterface $guzzle, $enableJsonResponse)
    {
        $this->guzzle = $guzzle;
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
        if (!$this->isSpotCredentialValid($spotCredential)) {
            throw new \BadMethodCallException(
                "Check spot credential data, some value invalid: ".print_r($spotCredential, true)
            );
        }

        $this->guzzle->setBaseUrl($spotCredential['url']);

        return $this->guzzle->post(
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
}
