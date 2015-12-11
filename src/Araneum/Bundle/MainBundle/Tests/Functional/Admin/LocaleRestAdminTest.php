<?php

namespace Functional\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LocaleRestAdminTest
 *
 */
class LocaleRestAdminTest extends BaseController
{
    /**
     * Test GetLocaleJson default
     *
     * @runInSeparateProcess
     */
    public function testGetLocaleJsonReturnEmptyValue()
    {
        $client = self::createAdminAuthorizedClient('admin');
        $client->request(
            'GET',
            '/manage/locales/locale/0',
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
    }

    /**
     * Test GetLocaleJson by id
     *
     * @runInSeparateProcess
     */
    public function testGetLocaleJson()
    {
        $client = self::createAdminAuthorizedClient('admin');
        $locale = $client
            ->getContainer()
            ->get('doctrine.orm.default_entity_manager')
            ->getRepository('AraneumMainBundle:Locale')
            ->findOneByName('TestLocaleName');
        $client->request(
            'GET',
            '/manage/locales/locale/'.$locale->getId(),
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $response = $client->getResponse();
        $decoded = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertNotEmpty($decoded['vars']['data']);
    }

    /**
     * test saveLocalePost create locale
     *
     * @runInSeparateProcess
     */
    public function testSaveLocalePostCreate()
    {
        $client = self::createAdminAuthorizedClient('admin');
        $em = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $locale = $em
            ->getRepository('AraneumMainBundle:Locale')
            ->findOneByLocale('am');
        if (!empty($locale)) {
            $em->remove($locale);
            $em->flush();
        }
        $localeData = [
            'name' => time(),
            'locale' => 'am',
            'enabled' => true,
            'orientation' => 1,
            'encoding' => 'UTF-8',
        ];
        $client->request(
            'POST',
            '/manage/locales/locale/save',
            $localeData,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $response = $client->getResponse();
        $decoded = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('Locale has been saved', $decoded['message']);
    }

    /**
     * test saveLocalePost update locale
     *
     * @runInSeparateProcess
     */
    public function testSaveLocalePostUpdate()
    {
        $client = self::createAdminAuthorizedClient('admin');
        $locale = $client
            ->getContainer()
            ->get('doctrine.orm.default_entity_manager')
            ->getRepository('AraneumMainBundle:Locale')
            ->findOneByName('TestLocaleName');
        $localeData = [
            'id' => $locale->getId(),
            'name' => 'TestLocaleName',
            'locale' => 'or',
            'enabled' => true,
            'orientation' => 1,
            'encoding' => 'UTF-16',
        ];
        $client->request(
            'POST',
            '/manage/locales/locale/save',
            $localeData,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $response = $client->getResponse();
        $decoded = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_ACCEPTED, $response->getStatusCode());
        $this->assertEquals('Locale has been saved', $decoded['message']);
    }

    /**
     * test saveLocalePost bad request
     *
     * @runInSeparateProcess
     */
    public function testSaveLocalePostBadRequest()
    {
        $client = self::createAdminAuthorizedClient('admin');
        $localeData = [
            'name' => time(),
            'locale' => '',
            'enabled' => 1,
            'orientation' => 1,
            'encoding' => 'UTF-8',
        ];
        $expectedErrorMessage = 'locale:
    ERROR: This value should not be blank.';
        $client->request(
            'POST',
            '/manage/locales/locale/save',
            $localeData,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $response = $client->getResponse();
        $decoded = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals($expectedErrorMessage, trim($decoded['message']));
    }

    /**
     * test disabled packdata
     *
     * @runInSeparateProcess
     */
    public function testDisabledPack()
    {
        $client = self::createAdminAuthorizedClient('admin');

        $em = $client
            ->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $qb = $em->createQueryBuilder();

        $result = $qb->addSelect('l.id')
            ->add('from', 'Araneum\Bundle\MainBundle\Entity\Locale l')
            ->add('where', 'l.name LIKE :like')
            ->setParameter('like', '%Pack%')
            ->getQuery()
            ->getResult();

        $arrIdx = [];
        $arrIdx['data'] = [];

        foreach ($result as $res) {
            array_push($arrIdx['data'], $res['id']);
        }

        $client->request(
            'POST',
            '/manage/locales/locale/disable',
            $arrIdx,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('"Success"', $response->getContent());
    }

    /**
     * test delete packdata
     *
     * @runInSeparateProcess
     */
    public function testDeletePack()
    {
        $client = self::createAdminAuthorizedClient('admin');

        $em = $client
            ->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $qb = $em->createQueryBuilder();

        $result = $qb->addSelect('l.id')
            ->add('from', 'Araneum\Bundle\MainBundle\Entity\Locale l')
            ->add('where', 'l.name LIKE :like')
            ->setParameter('like', '%DeletePack%')
            ->getQuery()
            ->getResult();

        $arrIdx = [];
        $arrIdx['data'] = [];

        foreach ($result as $res) {
            array_push($arrIdx['data'], $res['id']);
        }
        $client->request(
            'POST',
            '/manage/locales/locale/delete',
            $arrIdx,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('"Success"', $response->getContent());
    }
}
