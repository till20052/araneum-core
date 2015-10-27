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
    const CLUSTER_NAME = 'DefaultUltratradeCluster';
    const CLUSTER_TYPE = 1;
    const CLUSTER_ENABLED = true;
    const CLUSTER_STATUS = 1;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $cluster = $manager->getRepository('AraneumMainBundle:Cluster')
            ->findOneByName(self::CLUSTER_NAME);
        if (empty($cluster)) {
            $cluster = new Cluster();
            $cluster->setName(self::CLUSTER_NAME);
            $cluster->setHosts(
                new ArrayCollection(
                    [
                        $this->getReference('connectionHost'),
                        $this->getReference('connectionDb')
                    ]
                )
            );
            $cluster->setType(self::CLUSTER_TYPE);
            $cluster->setEnabled(self::CLUSTER_ENABLED);
            $cluster->setStatus(self::CLUSTER_STATUS);
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