<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Base\Tests\Fixtures\Main\LocaleFixtures;
use Araneum\Bundle\MainBundle\Entity\Locale;

class LocaleAdminTest extends BaseAdminController
{
    protected $createRoute = 'admin_araneum_main_locale_create';
    protected $updateRoute = 'admin_araneum_main_locale_edit';
    protected $deleteRoute = 'admin_araneum_main_locale_delete';
    protected $listRoute   = 'admin_araneum_main_locale_list';


    /**
     * Set up Before class
     */
    public static function setUpBeforeClass()
    {
        $client = static::createClient();
        $manager = $client->getContainer()
                    ->get('doctrine.orm.entity_manager');

        $repository = $manager
                      ->getRepository('AraneumMainBundle:Locale');

        $create = $repository->findOneByName('localeCreate');

        if($create){
            $manager->remove($create);
            $manager->flush();
        }

        $filter= $repository->findOneByName(LocaleFixtures::TEST_LOC_NAME_FILTER);

        if(!$filter){
            $filterLocale = new Locale();
            $filterLocale->setName(LocaleFixtures::TEST_LOC_NAME_FILTER)
                         ->setLocale(LocaleFixtures::TEST_LOC_LOCALE_FILTER)
                         ->setEncoding(LocaleFixtures::TEST_LOC_ENCOD_FILTER)
                         ->setOrientation(LocaleFixtures::TEST_LOC_ORIENT);


            $manager->persist($filterLocale);
            $manager->flush();
        }

       $update = $repository->findOneByName(LocaleFixtures::TEST_LOC_NAME_UPDATE);

        if(!$update){
            $updateLocale = new Locale();
            $updateLocale->setName(LocaleFixtures::TEST_LOC_NAME_UPDATE)
                ->setLocale('bg_BG')
                ->setEncoding(LocaleFixtures::TEST_LOC_ENCOD)
                ->setOrientation(LocaleFixtures::TEST_LOC_ORIENT);


            $manager->persist($updateLocale);
            $manager->flush();
        }


        $delete = $repository->findOneByName(LocaleFixtures::TEST_LOC_NAME_DELETE);

        if(!$delete){
            $filterDelete = new Locale();
            $filterDelete->setName(LocaleFixtures::TEST_LOC_NAME_DELETE)
                         ->setLocale(LocaleFixtures::TEST_LOC_LOCALE_DELETE)
                         ->setEncoding(LocaleFixtures::TEST_LOC_ENCOD)
                         ->setOrientation(LocaleFixtures::TEST_LOC_ORIENT);

            $manager->persist($filterDelete);
            $manager->flush();
        }
    }

    /**
     * Set of arguments for testCreate method
     *
     * @return array
     */
    public function createDataSource()
    {
        return [
            'not valid locale' => [
                [
                    'name' => 'localeCreate',
                    'locale' => 'not valid',
                    'orientation' => Locale::ORIENT_LFT_TO_RGT,
                    'encoding' => 'testLocaleEncodingCreate',
                ],
                false
            ],
            'not valid name, too long' => [
                [
                    'name' => '123456789 123456789 123456789 123456789 ',
                    'locale' => 'not valid',
                    'orientation' => Locale::ORIENT_LFT_TO_RGT,
                    'encoding' => 'testLocaleEncodingCreate',
                ],
                false
            ],
            'empty name' => [
                [
                    'name' => '',
                    'locale' => 'ru_RU',
                    'orientation' => Locale::ORIENT_LFT_TO_RGT,
                    'encoding' => 'testLocaleEncodingCreate',
                ],
                false
            ],
            'not valid encoding, too long' => [
                [
                    'name' => 'localeCreate',
                    'locale' => 'ru_RU',
                    'orientation' => Locale::ORIENT_LFT_TO_RGT,
                    'encoding' => '123456789 123456789 123456789 123456789 ',
                ],
                false
            ],
            'empty encoding' => [
                [
                    'name' => 'localeCreate',
                    'locale' => 'bn_BD',
                    'orientation' => Locale::ORIENT_LFT_TO_RGT,
                    'encoding' => '',
                ],
                false
            ],
            'normal' => [
                [
                    'name' => 'localeCreate',
                    'locale' => 'eu_ES',
                    'orientation' => Locale::ORIENT_LFT_TO_RGT,
                    'enabled' => false,
                    'encoding' => LocaleFixtures::TEST_LOC_ENCOD,
                ],
                true
            ],

        ];
    }

    /**
     * Set of arguments for testFilter method
     *
     * @return array
     */
    public function filterDataSource()
    {
        $locale = self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Locale')
            ->findOneBy(['name' => LocaleFixtures::TEST_LOC_NAME_FILTER]);


        return
            [
                'found' => [
                    [
                        'filter[name][value]' => LocaleFixtures::TEST_LOC_NAME_FILTER,
                        'filter[locale][value]' => LocaleFixtures::TEST_LOC_LOCALE_FILTER,
                        'filter[orientation][value]' => LocaleFixtures::TEST_LOC_ORIENT_FILTER,
                        'filter[encoding][value]' => LocaleFixtures::TEST_LOC_ENCOD_FILTER,
                        'filter[createdAt][value][start]' => '01/01/1979',
                        'filter[createdAt][value][end]' => '01/01/2050',
                    ],
                    true,
                    $locale,
                ],
                'not found' => [
                    [
                        'filter[name][value]' => LocaleFixtures::TEST_LOC_LOCALE_UPDATE,
                    ],
                    false,
                    $locale,
                ],

            ];
    }

    /**
     * Set of arguments for testUpdate method
     *
     * @return array
     */
    public function updateDataSource()
    {

        $locale = self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Locale')
            ->findOneByName(LocaleFixtures::TEST_LOC_NAME_UPDATE);


        return [
            'not uniq locale' => [
                [
                    'locale' => 'en_US',
                ],
                false,
                $locale
            ],
            'not uniq name' => [
                [

                    'name' => LocaleFixtures::TEST_LOC_NAME_FILTER
                ],
                false,
                $locale
            ],
            'normal' => [
                [
                    'name' => LocaleFixtures::TEST_LOC_NAME_UPDATE,
                    'locale' => 'mk_MK',
                    'orientation' => Locale::ORIENT_LFT_TO_RGT,
                    'enabled' => false,
                ],
                true,
                $locale
            ],

        ];
    }

    /**
     * Return entity for testDelete method
     *
     * @return mixed
     */
    public function deleteDataSource()
    {
        $client = static::createClient();

        return $client
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Locale')
            ->findOneByName(LocaleFixtures::TEST_LOC_NAME_DELETE);
    }

}