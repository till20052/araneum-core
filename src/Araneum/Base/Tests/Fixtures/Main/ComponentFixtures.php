<?php

namespace Araneum\Base\Tests\Fixtures\Main;

use Araneum\Bundle\MainBundle\Entity\Component;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ComponentFixtures extends AbstractFixture implements FixtureInterface
{
    const TEST_COMP_NAME = 'TestComponent';
    const TEST_COMP_DESC = 'Test component description';
    const TEST_COMP_ENABLED = true;
    const TEST_COMP_DEFAULT = true;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $component = $manager->getRepository('AraneumMainBundle:Component')->findOneByName(self::TEST_COMP_NAME);
        if (empty($component)) {
            $component = new Component();
            $component->setName(self::TEST_COMP_NAME);
            $component->setDescription(self::TEST_COMP_DESC);
            $component->setEnabled(self::TEST_COMP_ENABLED);
            $component->setDefault(self::TEST_COMP_DEFAULT);
            $manager->persist($component);
            $manager->flush();
        }
        $this->addReference('component', $component);
    }
}