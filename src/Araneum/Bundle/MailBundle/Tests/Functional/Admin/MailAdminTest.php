<?php

namespace Araneum\Bundle\MailBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Mail\MailFixtures;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DomCrawler\Crawler;

class MailAdminTest extends BaseController
{
    /**
     * Test is create action is disabled
     *
     *
     */
    public function testDisableCreate()
    {
        $client = $this->createAdminAuthorizedClient();

        $crawler = $client->request(
            'GET',
            '/en/admin/araneum/mail/mail/create'
        );

        $this->assertFalse($client->getResponse()->isSuccessful());
    }

    /**
     * Test is edit action is disabled
     *
     *
     */
    public function testDisableEdit()
    {
        $client = $this->createAdminAuthorizedClient();

        $mail = $client->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMailBundle:Mail')
            ->findOneBySender(MailFixtures::TEST_MAIL_SENDER);

        $crawler = $client->request(
            'GET',
            '/en/admin/araneum/mail/mail/' . $mail->getId() . '/edit'
        );

        $this->assertFalse($client->getResponse()->isSuccessful());
    }

    /**
     * Test is delete action is disabled
     *
     *
     */
    public function testDisableDelete()
    {
        $client = $this->createAdminAuthorizedClient();

        $mail = $client->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMailBundle:Mail')
            ->findOneBySender(MailFixtures::TEST_MAIL_SENDER);

        $crawler = $client->request(
            'GET',
            '/en/admin/araneum/mail/mail/' . $mail->getId() . '/delete'
        );

        $this->assertFalse($client->getResponse()->isSuccessful());
    }

    /**
     * Show test
     *
     *
     */
    public function testShow()
    {
        $client = $this->createAdminAuthorizedClient();

        $mail = $client->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMailBundle:Mail')
            ->findOneBySender(MailFixtures::TEST_MAIL_SENDER);

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('admin_araneum_mail_mail_show', ['id' => $mail->getId()])
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * Set data for Filter test
     *
     * @return array
     * @throws EntityNotFoundException
     */
    public function filterDataSource()
    {
        $manager = static::createClient()->getContainer()
            ->get('doctrine.orm.entity_manager');
        $mail = $manager
            ->getRepository('AraneumMailBundle:Mail')
            ->findOneBySender(MailFixtures::TEST_MAIL_SENDER);

        return [
            'normal' => [
                [
                    'filter[application][value]' => $mail->getApplication()->getId(),
                    'filter[sender][value]' => MailFixtures::TEST_MAIL_SENDER,
                    'filter[target][value]' => MailFixtures::TEST_MAIL_TARGET,
                    'filter[headline][value]' => MailFixtures::TEST_MAIL_HEADLINE,
                    'filter[status][value]' => MailFixtures::TEST_MAIL_STATUS,
                    'filter[textBody][value]' => MailFixtures::TEST_MAIL_TEXTBODY,
                    'filter[sentAt][value][start]' => '08/24/1979',
                    'filter[sentAt][value][end]' => '08/24/2015',
                    'filter[createdAt][value][start]' => '08/24/1979',
                    'filter[createdAt][value][end]' => '08/24/2015',
                ],
                true,
                $mail
            ],
            'non exist' => [
                [
                    'filter[application][value]' => $mail->getApplication()->getId(),
                    'filter[sender][value]' => md5(uniqid(null, true)),
                    'filter[target][value]' => MailFixtures::TEST_MAIL_TARGET,
                    'filter[headline][value]' => MailFixtures::TEST_MAIL_HEADLINE,
                    'filter[status][value]' => MailFixtures::TEST_MAIL_STATUS,
                    'filter[textBody][value]' => MailFixtures::TEST_MAIL_TEXTBODY,
                    'filter[sentAt][value][start]' => '08/24/1979',
                    'filter[sentAt][value][end]' => '08/24/2015',
                    'filter[createdAt][value][start]' => '08/24/1979',
                    'filter[createdAt][value][end]' => '08/24/2015',
                ],
                false,
                $mail
            ]
        ];
    }

    /**
     * Filter Test
     *
     * @dataProvider filterDataSource
     *
     *
     * @param array $fullFormInput
     * @param       $expected
     * @param       $entity
     */
    public function testFilter(array $fullFormInput, $expected, $entity)
    {
        if (!method_exists($entity, 'getId')) {
            throw new \BadMethodCallException('Entity must contains getId method');
        }

        $client = $this->createAdminAuthorizedClient();
        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('admin_araneum_mail_mail_list', ['_locale' => 'en'])
        );

        $form = $crawler->selectButton('Filter')->form($fullFormInput);
        $crawler = $client->submit($form);

        $list = $crawler->filter('table.table > tbody > tr > td:nth-child(1)')
            ->each(
                function (Crawler $node) {
                    return (int)$node->text();
                }
            );

        $this->assertEquals($expected, in_array($entity->getId(), $list));
    }

}