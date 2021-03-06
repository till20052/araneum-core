<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Araneum\Bundle\AgentBundle\Entity\Country;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use RuntimeException;

/**
 * Class LoadCountryData
 *
 * @package Araneum\Bundle\UserBundle\DataFixtures\ORM
 */
class LoadCountryData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            'Araneum\Bundle\MainBundle\DataFixtures\ORM\ApplicationData',
        ];
    }

    /**
     * Fixtures for country
     *
     * @param ObjectManager $manager
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $application = $this->getReference('application');
        if (!empty($application)) {
            $data = $this->container->get('araneum.base.spot_api')->get(
                [
                    'MODULE' => 'Country',
                    'COMMAND' => 'view',
                ],
                $application->getSpotCredential()
            );
            if (!empty($data)) {
                foreach ($data as $countryData) {
                    $country = $manager->getRepository('AraneumAgentBundle:Country')->findOneBy(
                        ['name' => $countryData['iso']]
                    );
                    if (empty($country)) {
                        $country = new Country();
                        $country->setId($countryData['id']);
                        $country->setName($countryData['iso']);
                        $country->setTitle($countryData['name']);
                        $country->setPhoneCode(!empty($countryData['prefix']) ? $countryData['prefix'] : null);
                        $manager->persist($country);
                    }
                }
                $manager->flush();
            } else {
                throw new RuntimeException('Failed to parse countries');
            }
        }
    }
}
