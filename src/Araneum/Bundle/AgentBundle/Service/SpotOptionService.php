<?php

namespace Araneum\Bundle\AgentBundle\Service;

use Araneum\Base\Service\Spot\SpotApiSenderService;
use Araneum\Bundle\MainBundle\Entity\Application;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * Class SpotOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotOptionService
{
    protected $spotApiSenderService;
    protected $spotApiPublicUrlLogin;

    /**
     * SpotOptionService constructor.
     *
     * @param SpotApiSenderService $spotApiSenderService
     * @param string               $spotApiPublicUrlLogin
     */
    public function __construct(SpotApiSenderService $spotApiSenderService, $spotApiPublicUrlLogin)
    {
        $this->spotApiPublicUrlLogin = $spotApiPublicUrlLogin;
        $this->spotApiSenderService = $spotApiSenderService;
    }

    /**
     * SpotOption Login
     *
     * @param string      $email
     * @param string      $password
     * @param Application $application
     * @return array|false
     */
    public function login($email, $password, $application)
    {
        $requestData = [
            'email' => $email,
            'password' => $password,
        ];
        $response = $this->spotApiSenderService->sendToPublicUrl(
            Request::POST,
            $this->spotApiPublicUrlLogin,
            $requestData,
            $application
        );

        if ($this->responseIsSuccessful($response)) {
            $decodedResponse = $response->json();

            return [
                'spotsession' => $this->getSpotSession($response->getSetCookie()),
                'customerId' => $decodedResponse['customerId'],
            ];
        } else {
            return false;
        }
    }

    /**
     * Reset Customer Password on SpotOption
     *
     * @param string $login
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     */
    public function resetPassword($login, $currentPassword, $newPassword)
    {
        $login = null;
        $currentPassword = null;
        $newPassword = null;

        return true;
    }

    /**
     * Check is response is successful
     *
     * @param Response $response
     * @return bool
     */
    public function responseIsSuccessful(Response $response)
    {
        $decodedSpotResponse = $response->json();

        return array_key_exists('status', $decodedSpotResponse) && $decodedSpotResponse['status'] === true;
    }

    /**
     * Get spotsession from cookies
     *
     * @param $cookie
     * @return mixed
     */
    private function getSpotSession($cookie)
    {
        preg_match('/spotsession.{10,15}=(.{32}); /', $cookie, $matches);
        if (!array_key_exists(1, $matches)) {
            throw new \BadMethodCallException('Cookie must contains spotsession, cookie: '.$cookie);
        }

        return $matches[1];
    }
}
