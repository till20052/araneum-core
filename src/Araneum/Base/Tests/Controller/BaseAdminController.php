<?php

namespace Araneum\Base\Tests\Controller;

use Araneum\Base\Tests\Admin\AdminTestInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

abstract class BaseAdminController extends BaseController implements AdminTestInterface
{
    protected $createRoute = null;
    protected $updateRoute = null;
    protected $deleteRoute = null;
    protected $listRoute   = null;

    /**
     * Base test of create entity in Sonata Admin
     *
     * @dataProvider createDataSource
     * @runInSeparateProcess
     *
     * @param array $formInput
     * @param       $expected
     */
    public function testCreate(array $formInput, $expected)
    {
        if (empty($this->createRoute)) {
            throw new \BadMethodCallException('You must specify createRoute field');
        }

        $client = $this->createAdminAuthorizedClient();
        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate($this->createRoute)
        );

        $form = $crawler->selectButton('btn_create_and_edit')->form();

        $form->setValues($this->formatFormInput($formInput, $form));

        $crawler = $client->submit($form);

        $this->assertEquals($expected, count($crawler->filter('.alert-danger')) <= 0, $crawler->html());
    }

    /**
     * Base test of edit entity in Sonata Admin
     *
     * @dataProvider updateDataSource
     * @runInSeparateProcess
     *
     * @param array $formInput
     * @param       $expected
     * @param       $entity
     */
    public function testUpdate(array $formInput, $expected, $entity)
    {
        if (empty($this->updateRoute)) {
            throw new \BadMethodCallException('You must specify updateRoute field');
        }

        if (!method_exists($entity, 'getId')) {
            throw new \BadMethodCallException('Entity must contains getId method');
        }

        $client = $this->createAdminAuthorizedClient();
        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate($this->updateRoute, ['id' => $entity->getId()])
        );

        $form = $crawler->selectButton('btn_update_and_edit')->form();
        $form->setValues($this->formatFormInput($formInput, $form));

        $crawler = $client->submit($form);

        $this->assertEquals($expected, count($crawler->filter('.alert-danger')) <= 0, $crawler->html());
    }

    /**
     * Base test of filter in Sonata Admin.
     *
     * @dataProvider filterDataSource
     * @runInSeparateProcess
     *
     * @param array $fullFormInput
     * @param       $expected
     * @param       $entity
     */
    public function testFilter(array $fullFormInput, $expected, $entity)
    {
        if (empty($this->listRoute)) {
            throw new \BadMethodCallException('You must specify listRoute field');
        }

        if (!method_exists($entity, 'getId')) {
            throw new \BadMethodCallException('Entity must contains getId method');
        }

        $client = $this->createAdminAuthorizedClient();
        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate($this->listRoute, ['_locale' => 'en'])
        );

        $form = $crawler->selectButton('Filter')->form($fullFormInput);
        $crawler = $client->submit($form);

        $list = $crawler->filter('table.table > tbody > tr > td:nth-child(2) > a')
            ->each(
                function (Crawler $node) {
                    if($node->count()){
                        return (int)$node->text();
                    }
                }
            );

        $this->assertEquals($expected, in_array($entity->getId(), $list), var_dump($list->text()) . $crawler->html());
    }

    /**
     * Base test of delete in Sonata Admin
     *
     * @runInSeparateProcess
     */
    public function testDelete()
    {
        $entity = $this->deleteDataSource();

        if (empty($this->deleteRoute)) {
            throw new \BadMethodCallException('You must specify deleteRoute field');
        }

        if (!method_exists($entity, 'getId')) {
            throw new \BadMethodCallException('Entity must contains getId method');
        }

        $client = $this->createAdminAuthorizedClient();
        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate(
                $this->deleteRoute,
                [
                    'id' => $entity->getId(),
                    '_locale' => 'en',
                ]
            )
        );

        $form = $crawler->selectButton('Yes, delete')->form();
        $client->submit($form, ['_method' => 'DELETE']);

        $entityFromDb = $client->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository(get_class($entity))->find($entity->getId());

        $this->assertTrue(empty($entityFromDb), $crawler->html());
    }

    /**
     * Prepare form field for test Symfony Forms
     *
     * @param array $formInput
     * @param Form  $form
     * @return array
     */
    protected function formatFormInput(array $formInput, Form $form)
    {
        $formPrefix = $this->getFormPrefix($form);

        $formatFormInput = [];
        foreach ($formInput as $name => $value) {
            $formatFormInput[$formPrefix . '[' . $name . ']'] = $value;
        }

        return $formatFormInput;
    }

    /**
     * Get Symfony form name from DOMCrawler\Form
     *
     * @param $form
     * @return string
     */
    protected function getFormPrefix(Form $form)
    {
        return key(array_slice($form->getPhpValues(), 1, 1));
    }
}