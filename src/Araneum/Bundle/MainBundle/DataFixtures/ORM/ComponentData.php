<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Araneum\Bundle\MainBundle\Entity\Component;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ComponentData
 *
 * @package Araneum\Bundle\MainBundle\DataFixtures\ORM
 */
class ComponentData extends AbstractFixture implements FixtureInterface
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
        $component = $manager->getRepository('AraneumMainBundle:Component')
            ->findOneByName('DefaultUltratradeComponent');
        if (empty($component)) {
            $component = new Component();
            $component->setName('DefaultUltratradeComponent');
            $component->setDescription('description');
            $component->setEnabled(true);
            $component->setDefault(true);
            $component->setOptions(
                [
                    'option1' => 'param1',
                ]
            );
            $manager->persist($component);
            $manager->flush();
        }
        $this->addReference('component', $component);
    }

    private function setIxoption(ObjectManager $manager)
    {
        $component = $manager->getRepository('AraneumMainBundle:Component')
            ->findOneByName('DefaultIxoptionComponent');
        if (empty($component)) {
            $component = new Component();
            $component->setName('DefaultIxoptionComponent');
            $component->setDescription('description');
            $component->setEnabled(true);
            $component->setDefault(true);
            $component->setOptions(
                [
                    'option1' => 'param1',
                ]
            );
            $manager->persist($component);
            $manager->flush();
        }
        $this->addReference('componentIxoption', $component);
    }
}
