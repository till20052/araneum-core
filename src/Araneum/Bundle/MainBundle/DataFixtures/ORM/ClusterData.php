<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ClusterData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $cluster = $manager->getRepository('AraneumMainBundle:Cluster')
            ->findOneByName('DefaultUltratradeCluster');
        if (empty($cluster)) {
            $cluster = new Cluster();
            $cluster->setName('DefaultUltratradeCluster');
            $cluster->setHosts(
                new ArrayCollection(
                    [
                        $this->getReference('connectionHost')
                    ]
                )
            );
            $cluster->setType(1);
            $cluster->setEnabled(true);
            $cluster->setStatus(1);
            $manager->persist($cluster);
            $manager->flush();
        }
        $this->addReference('cluster', $cluster);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return ['Araneum\Bundle\MainBundle\DataFixtures\ORM\ConnectionData'];
    }
}