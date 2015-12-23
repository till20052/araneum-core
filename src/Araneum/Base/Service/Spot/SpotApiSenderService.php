<?php

namespace Araneum\Base\Service\Spot;

use Araneum\Bundle\MainBundle\Entity\Application;
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
     * Send request to spot public api url
     *
     * @param string      $method      HTTP method. Defaults to GET
     * @param string      $path
     * @param array       $requestData
     * @param Application $application
     * @return \Guzzle\Http\Message\Response
     */
    public function sendToPublicUrl($method, $path, array $requestData, Application $application)
    {
        $spotApiPublicUrl = $application->getSpotApiPublicUrl();
        if (!filter_var($spotApiPublicUrl, FILTER_VALIDATE_URL)) {
            throw new \BadMethodCallException(
                "Not valid spot public utl: ".$spotApiPublicUrl
            );
        }

        $this->guzzle->setBaseUrl($spotApiPublicUrl);

        return $this->guzzle->createRequest($method, $path, null, $requestData)->send();
    }
}
