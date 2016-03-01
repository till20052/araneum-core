<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ClusterData
 *
 * @package Araneum\Bundle\MainBundle\DataFixtures\ORM
 */
class ClusterData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $cluster = $manager->getRepository('AraneumMainBundle:Cluster')->findOneByName('DefaultUltratradeCluster');

        if (empty($cluster)) {
            $cluster = new Cluster();
            $cluster->setName('DefaultUltratradeCluster');
            $cluster->setType(1);
            $cluster->setEnabled(true);
            $cluster->setStatus(1);
            $manager->persist($cluster);
            $manager->flush();
        }

        $this->addReference('cluster', $cluster);
    }
}
