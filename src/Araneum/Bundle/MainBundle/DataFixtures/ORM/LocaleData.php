<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Araneum\Bundle\MainBundle\Entity\Locale;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadLocaleData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $localeEn = $manager->getRepository('AraneumMainBundle:Locale')->findOneByLocale('en_US');
        if (empty($localeEn)) {
            $localeEn = new Locale();
            $localeEn->setName('en');
            $localeEn->setLocale('en_US');
            $localeEn->setEnabled(true);
            $localeEn->setOrientation(1);
            $localeEn->setEncoding('UTF-8');
            $manager->persist($localeEn);
            $manager->flush();
        }
    }
}