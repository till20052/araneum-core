<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MailApiControllerTest extends BaseController
{
    /**
     * Test post mail api
     *
     * @dataProvider postMailData
     * @runInSeparateProcess
     * @param $parameters
     * @param $expectedHTTPCode
     */
    public function testPostMail($parameters, $expectedHTTPCode)
    {
        $client = self::createAdminAuthorizedClient('api');

        $client->request('POST', 'en/mail/api/mail?appKey=' . ApplicationFixtures::TEST_APP_APP_KEY, $parameters);

        $this->assertEquals(
            $expectedHTTPCode,
            $client->getResponse()->getStatusCode(),
            $client->getResponse()->getContent()
        );
    }

    /**
     * Data provider for testPostMail
     *
     * @return array
     */
    public function postMailData()
    {
        return [
            'normal' => [
                [
                    'target' => 'test@test.com, Test test',
                    'sender' => 'test@test.com, Test test',
                    'headline' => 'Test Headline',
                    'text_body' => 'Test body',
                    'html_body' => 'Html body',
                ],
                Response::HTTP_CREATED,
            ],
            'empty fields' => [
                [
                    'target' => '',
                    'sender' => '',
                    'headline' => '',
                    'text_body' => '',
                    'html_body' => '',
                    'attachment' => '',
                ],
                Response::HTTP_BAD_REQUEST,
            ],
            'extra fields' => [
                [
                    'ninja' => 'turtle',
                ],
                Response::HTTP_BAD_REQUEST,
            ],
        ];
    }
}