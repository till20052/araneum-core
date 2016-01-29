<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Araneum\Bundle\AgentBundle\Entity\Country;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\RadBundle\DataFixtures\AbstractFixture;
use RuntimeException;

/**
 * Class LoadCountryData
 *
 * @package Araneum\Bundle\UserBundle\DataFixtures\ORM
 */
class LoadCountryData extends AbstractFixture
{
    /**
     * Fixtures for country
     *
     * @param ObjectManager $manager
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $data = $this->container->get('araneum.base.spot_api')->get(
            [
                'MODULE' => 'Country',
                'COMMAND' => 'view',
            ],
            [
                'url' => 'http://api-spotplatform.ultratrade.com/Api',
                'userName' => 'araneum',
                'password' => 'wU7tc2YKg2',
            ]
        );
        if (!empty($data)) {
            foreach ($data as $countries) {
                $country = $manager->getRepository('AraneumAgentBundle:Country')->findOneBy(
                    ['name' => $countries['iso']]
                );
                if (empty($country)) {
                    $country = new Country();
                    $country->setName($countries['iso']);
                    $country->setTitle($countries['name']);
                    $country->setPhoneCode(!empty($countries['prefix']) ? $countries['prefix'] : null);
                    $country->setSpotId($countries['id']);
                    $manager->persist($country);
                }
            }
        } else {
            throw new RuntimeException('Failed to parse countries');
        }

        $manager->flush();
    }
}
