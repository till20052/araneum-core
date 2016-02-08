<?php

namespace Araneum\Base\Service\Spot;

use Araneum\Base\Service\AbstractApiSender;
use Araneum\Bundle\AgentBundle\Entity;

/**
 * Class SpotApiSenderService
 *
 * @package Araneum\Base\Service\Guzzle
 */
class SpotApiSenderService extends AbstractApiSender
{
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
     * @param array $requestData
     * @param array $spotCredential
     * @return bool
     */
    public function prepareToSend($requestData, $spotCredential)
    {
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

        return $this->guzzle->post(null, null, $body)->send();
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
