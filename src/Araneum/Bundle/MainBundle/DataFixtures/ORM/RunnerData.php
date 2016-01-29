<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Araneum\Bundle\MainBundle\Entity\Runner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class RunnerData
 *
 * @package Araneum\Bundle\MainBundle\DataFixtures\ORM
 */
class RunnerData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->setDefault($manager);
        $this->setIxoption($manager);
    }

    private function setDefault(ObjectManager $manager)
    {
        $runner = $manager->getRepository('AraneumMainBundle:Runner')
            ->findOneByName('DefaultUltratradeRunner');
        if (empty($runner)) {
            $runner = new Runner();
            $runner->setName('DefaultUltratradeRunner');
            $runner->setCluster($this->getReference('clusteIxoptionr'));
            $runner->setType(1);
            $runner->setEnabled(true);
            $runner->setStatus(1);
            $runner->setDomain('ultratrade.com');
            $manager->persist($runner);
            $manager->flush();
        }
        $this->addReference('runner', $runner);
    }

    private function setIxoption(ObjectManager $manager)
    {
        $runner = $manager->getRepository('AraneumMainBundle:Runner')
            ->findOneByName('DefaultIxoptionRunner');
        if (empty($runner)) {
            $runner = new Runner();
            $runner->setName('DefaultIxoptionRunner');
            $runner->setCluster($this->getReference('clusterIxoption'));
            $runner->setType(1);
            $runner->setEnabled(true);
            $runner->setStatus(1);
            $runner->setDomain('ixoption.com');
            $manager->persist($runner);
            $manager->flush();
        }
        $this->addReference('runnerIxoption', $runner);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            'Araneum\Bundle\MainBundle\DataFixtures\ORM\ClusterData',
        ];
    }
}
