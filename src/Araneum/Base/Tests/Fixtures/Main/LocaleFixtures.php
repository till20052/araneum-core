<?php

namespace Araneum\Base\Tests\Fixtures\Main;

use Araneum\Bundle\MainBundle\Entity\Locale;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\RadBundle\DataFixtures\AbstractFixture;

class LocaleFixtures extends AbstractFixture implements FixtureInterface
{
    const TEST_LOC_NAME = 'TestLocaleName';
    const TEST_LOC_LOCALE = 'TestLoc';
    const TEST_LOC_ENABLED = true;
    const TEST_LOC_ORIENT = 1;
    const TEST_LOC_ENCOD = 'TestEncoding';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $locale = $manager->getRepository('AraneumMainBundle:Locale')->findOneByName(self::TEST_LOC_NAME);
        if (empty($locale)) {
            $locale = new Locale();
            $locale->setName(self::TEST_LOC_NAME);
            $locale->setLocale(self::TEST_LOC_LOCALE);
            $locale->setEnabled(self::TEST_LOC_ENABLED);
            $locale->setOrientation(self::TEST_LOC_ORIENT);
            $locale->setEncoding(self::TEST_LOC_ENCOD);
            $manager->persist($locale);
            $manager->flush();
        }
        $this->addReference('locale', $locale);
    }
}