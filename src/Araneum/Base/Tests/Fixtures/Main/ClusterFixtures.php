<?php

namespace Araneum\Base\Tests\Fixtures\Main;

use Araneum\Bundle\MainBundle\Entity\Cluster;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;

class ClusterFixtures extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    const TEST_CLU_NAME = 'TestClusterName';
    const TEST_CLU_TYPE = 1;
    const TEST_CLU_ENABLED = true;
    const TEST_CLU_STATUS = 1;

    const TEST_TEMP_CLU_NAME = 'TestTempName';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $cluster = $manager->getRepository('AraneumMainBundle:Cluster')->findOneByName(self::TEST_CLU_NAME);
        if (empty($cluster)) {
            $cluster = new Cluster();
            $cluster->setName(self::TEST_CLU_NAME);
            $cluster->setHosts(new ArrayCollection([$this->getReference('connectionHost')]));
            $cluster->setType(self::TEST_CLU_TYPE);
            $cluster->setEnabled(self::TEST_CLU_ENABLED);
            $cluster->setStatus(self::TEST_CLU_STATUS);
            $manager->persist($cluster);
            $manager->flush();
        }
        $this->addReference('cluster', $cluster);

        $clusterTmp = $manager->getRepository('AraneumMainBundle:Cluster')->findOneByName(self::TEST_TEMP_CLU_NAME);
        if (empty($clusterTmp)) {
            $clusterTmp = new Cluster();
            $clusterTmp->setName(self::TEST_TEMP_CLU_NAME);
            $clusterTmp->setHosts(new ArrayCollection([$this->getReference('connectionHost')]));
            $clusterTmp->setType(self::TEST_CLU_TYPE);
            $clusterTmp->setEnabled(self::TEST_CLU_ENABLED);
            $clusterTmp->setStatus(self::TEST_CLU_STATUS);
            $manager->persist($clusterTmp);
            $manager->flush();
        }

    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return ['Araneum\Base\Tests\Fixtures\Main\ConnectionFixtures'];
    }
}