<?php

namespace Araneum\Bundle\AgentBundle\Tests\Unit\Form\Type;

use Araneum\Bundle\AgentBundle\Entity\Lead;
use Araneum\Bundle\AgentBundle\Form\Type\LeadType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;

/**
 * Class LeadTypeTest
 *
 * @package Araneum\Bundle\AgentBundle\Tests\Unit\From\Type
 */
class LeadTypeTest extends KernelTestCase
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * Initialization
     */
    protected function setUp()
    {
        static::bootKernel();

        $this->formFactory = static::$kernel
            ->getContainer()
            ->get('form.factory');
    }

    /**
     * Data source
     *
     * @return array
     */
    public function dataSource()
    {
        return [
            'normal' => [
                [
                    'firstName' => 'Hugo',
                    'lastName' => 'Boss',
                    'country' => rand(1, 239),
                    'email' => 'hogo.boss@test.com',
                    'phone' => '380507894561',
                    'appKey' => md5(microtime(true)),
                ],
                true,
            ],
            'not valid email' => [
                [
                    'firstName' => 'Aston',
                    'lastName' => 'Martin',
                    'country' => rand(1, 239),
                    'email' => md5(microtime(true)),
                    'phone' => '380507894561',
                    'appKey' => md5(microtime(true)),
                ],
                false,
            ],
            'not valid phone' => [
                [
                    'firstName' => 'Aston',
                    'lastName' => 'Martin',
                    'country' => rand(1, 239),
                    'email' => 'hogo.boss@test.com',
                    'phone' => md5(microtime(true)),
                    'appKey' => md5(microtime(true)),
                ],
                false,
            ],
        ];
    }

    /**
     * Test LeadType
     *
     * @param array   $data
     * @param boolean $expected
     * @dataProvider dataSource
     * @runInSeparateProcess
     */
    public function testLeadType($data, $expected)
    {
        $lead = new Lead();
        $form = $this->formFactory
            ->create(new LeadType(), $lead)
            ->submit($data);

        $this->assertEquals($expected, $form->isValid());
        $this->assertCount($expected ? 0 : 1, $form->getErrors(true, false), $form->getErrors(true, false));
    }
}
