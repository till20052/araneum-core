<?php

namespace Araneum\Bundle\AgentBundle\Tests\Unit\From\Type;

use Araneum\Bundle\AgentBundle\Entity\Lead;
use Araneum\Bundle\AgentBundle\Form\Type\LeadType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;

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
			'scenario 1' => [
				[
					'firstName' => 'Hugo',
					'lastName' => 'Boss',
					'country' => rand(1, 239),
					'email' => 'hogo.boss@test.com',
					'phone' => '380507894561',
					'appKey' => md5(microtime(true))
				],
				function(Form $form, Lead $lead, KernelTestCase $testCase){
					$testCase->assertTrue($form->isValid());
					$testCase->assertEquals('hogo.boss@test.com', $lead->getEmail());
					$testCase->assertEquals('380507894561', $lead->getPhone());
				}
			],
			'scenario 2' => [
				[
					'firstName' => 'Aston',
					'lastName' => 'Martin',
					'country' => rand(1, 239),
					'email' => md5(microtime(true)),
					'phone' => md5(microtime(true)),
					'appKey' => md5(microtime(true))
				],
				function(Form $form, Lead $lead, KernelTestCase $testCase){
					$testCase->assertFalse($form->isValid());
					$testCase->assertCount(1, $form->get('email')->getErrors());
					$testCase->assertCount(1, $form->get('phone')->getErrors());
				}
			]
		];
	}

	/**
	 * Test LeadType
	 *
	 * @dataProvider dataSource
	 *
	 * @param array $data
	 * @param callable $assertions
	 */
	public function testLeadType($data, $assertions)
	{
		$lead = new Lead();

		$assertions(
			$this->formFactory
				->create(new LeadType(), $lead)
				->submit($data),
			$lead,
			$this
		);
	}
}