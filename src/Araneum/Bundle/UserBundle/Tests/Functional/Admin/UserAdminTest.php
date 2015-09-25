<?php
namespace Araneum\Bundle\UserBundle\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Component\DomCrawler\Form;

class UserAdminTest extends BaseController
{

    /**
     * Test main page
     *
     * @dataProvider saveProvider
     * @runInSeparateProcess
     */
    public function testSave($email, $username, $password, $expects)
    {
        $client = $this->createAdminAuthorizedClient();

        $router = $client->getContainer()->get('router');
        $crawler = $client->request('GET', $router->generate('admin_araneum_user_user_create'));

        $form = $crawler->selectButton('btn_create_and_edit')->form();
        $formPrefix = $this->getFormPrefix($form);
        $form->setValues(
            [
                $formPrefix . '[email]' => $email,
                $formPrefix . '[username]' => $username,
                $formPrefix . '[fullName]' => 'TestFullName',
                $formPrefix . '[plainPassword]' => $password,
            ]
        );

        $crawler = $client->submit($form);

        $this->assertEquals(count($crawler->filter('.alert-danger')) <= 0, $expects);
    }

    public function saveProvider()
    {
        return [
            [
                '12aemail@email.com',
                '1asdsad',
                'asdasd1',
                true
            ],
            [
                '12aemail@email.com',
                '1asdsad',
                'asdasd2',
                false
            ],
            [
                'nonValidEmail',
                '1asdsad',
                'asdasd2',
                false
            ],
            [
                'nonValidEmail',
                '1asdsad',
                '1',
                false
            ],
            [
                'nonValidEmail',
                '1asdsad',
                '1',
                false
            ],
        ];
    }

    /**
     * @param $form
     * @return mixed
     */
    private function getFormPrefix(Form $form)
    {
        return key(array_slice($form->getPhpValues(), 1, 1));
    }
}