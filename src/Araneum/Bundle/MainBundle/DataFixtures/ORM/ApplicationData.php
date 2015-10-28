<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ApplicationData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $app = $manager
            ->getRepository('AraneumMainBundle:Application')
            ->findOneByName('Ultratrade');

        if (empty($app)) {
            $app = new Application();
            $app->setName('Ultratrade');
            $app->setDomain('ultratrade.office.dev');
            $app->setPublic(true);
            $app->setEnabled(true);
            $app->setStatus(1);
            $app->setTemplate('DefaultTemplate');
            $app->setCluster($this->getReference('cluster'));
            $app->setDb($this->getReference('connectionDb'));
            $app->setLocales(new ArrayCollection([$this->getReference('locale')]));
            $app->setOwner($this->getReference('userAdmin'));
            $manager->persist($app);
            $manager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            'Araneum\Bundle\MainBundle\DataFixtures\ORM\LocaleData',
            'Araneum\Bundle\MainBundle\DataFixtures\ORM\ConnectionData',
            'Araneum\Bundle\MainBundle\DataFixtures\ORM\ClusterData',
            'Araneum\Bundle\UserBundle\DataFixtures\ORM\UserData'
        ];
    }
}