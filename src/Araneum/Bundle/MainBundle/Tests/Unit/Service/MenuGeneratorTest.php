<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Service;

use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Service\ApplicationManagerService;
use Araneum\Bundle\MainBundle\Service\MenuGeneratorService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class MenuGeneratorTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Unit\Service
 */
class MenuGeneratorTest extends \PHPUnit_Framework_TestCase
{
    private $inputArray;
    private $outputArray;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->inputArray = [
            'Root_Menu' => [
                'text' => 'Root Menu',
                'description' => 'description',
                'submenu' => [
                    'Submenu1' => [
                        'text' => 'submenu1',
                        'description' => 'description1',
                        'icon' => 'icon1',
                    ],
                    'Submenu2' => [
                        'text' => 'submenu2',
                        'description' => 'description2',
                        'icon' => 'icon2',
                    ],
                ],
            ],
        ];

        $this->outputArray = [
            [
                'text' => 'Root Menu',
                'description' => 'description',
                'heading' => 'true',
            ],
            [
                'text' => 'submenu1',
                'description' => 'description1',
                'icon' => 'icon1',
            ],
            [
                'text' => 'submenu2',
                'description' => 'description2',
                'icon' => 'icon2',
            ],
        ];
    }

    /**
     * Test generate one dimentional method
     *
     */
    public function testGenerateOneDimentional()
    {
        $generator = new MenuGeneratorService();
        $output = $generator->generateOneDimentional($this->inputArray);

        $this->assertEquals($this->outputArray, $output);
    }
}
