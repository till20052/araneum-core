<?php

namespace Araneum\Base\Service\Spot;

use Guzzle\Service\ClientInterface;

/**
 * Class SpotGuzzleClient
 *
 * @package Araneum\Base\Service\Guzzle
 */
class SpotApiSenderService
{
    /**
     * @var ClientInterface
     */
    protected $guzzle;

    /**
     * SpotApiService constructor.
     *
     * @param ClientInterface $guzzle
     */
    public function __construct(ClientInterface $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Send request to core
     *
     * @param array $requestData
     * @param array $spotCredential
     * @return \Guzzle\Http\Message\EntityEnclosingRequestInterface
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
                ],
                $requestData
            )
        );
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
