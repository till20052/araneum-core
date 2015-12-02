<?php

namespace Araneum\Base\Tests\Unit\Service\Actions;

use Araneum\Base\Service\Actions\ActionBuilder;
use Araneum\Base\Service\Actions\ActionBuilderInterface;

class ActionBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected $expectedAddOneAction = [
        "row" => [
            "deleteGroup" => [
                [
                    "resource" => "generatedUrl",
                ]
            ]
        ],
        "top" => [
            "deleteGroup" => [
                [
                    "resource" => "generatedUrl",
                ]
            ]
        ]
    ];
    protected $expectedAddTwoActionSameGroup = [
        "row" => [
            "deleteGroup" => [
                [
                    "resource" => "generatedUrl",
                ],
                [
                    "resource" => "generatedUrl",
                ],
            ]
        ],
        "top" => [
            "deleteGroup" => [
                [
                    "resource" => "generatedUrl",
                ],
                [
                    "resource" => "generatedUrl",
                ],
            ]
        ]
    ];
    protected $expectedAddTwoActionDifferentGroupDifferentPosition = [
        "row" => [
            "deleteGroup" => [
                [
                    "resource" => "generatedUrl",
                ],
            ]
        ],
        "top" => [
            "deleteGroup" => [
                [
                    "resource" => "generatedUrl",
                ]
            ],
            "addGroup" => [
                [
                    "resource" => "generatedUrl",
                ]
            ],
        ]
    ];

    /** @var  ActionBuilder */
    private $actionBuilder;

    /**
     * Set Up method
     */
    public function setUp()
    {
        $routerMock = $this->getMockBuilder('\Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock();

        $routerMock
            ->method('generate')
            ->will($this->returnValue('generatedUrl'));

        $this->actionBuilder = new ActionBuilder($routerMock);
    }

    /**
     * Data source for add method
     *
     * @return array
     */
    public function addDataSource()
    {
        return [
            'one action normal return' => [
                [
                    [
                        'group' => 'deleteGroup',
                        'options' => [
                            'resource' => 'deleteLocaleActionRoute',
                            'position' => ActionBuilderInterface::POSITION_ALL

                        ],
                    ],
                ],
                $this->expectedAddOneAction
            ],
            'two action same group normal return' => [
                [
                    [
                        'group' => 'deleteGroup',
                        'options' => [
                            'resource' => 'deleteLocaleActionRoute',
                            'position' => ActionBuilderInterface::POSITION_ALL

                        ],
                    ],
                    [
                        'group' => 'deleteGroup',
                        'options' => [
                            'resource' => 'deleteLocaleActionRoute',
                            'position' => ActionBuilderInterface::POSITION_ALL

                        ],
                    ],
                ],
                $this->expectedAddTwoActionSameGroup
            ],
            'two action different group different position normal return' => [
                [
                    [
                        'group' => 'deleteGroup',
                        'options' => [
                            'resource' => 'deleteLocaleActionRoute',
                            'position' => ActionBuilderInterface::POSITION_ALL

                        ],
                    ],
                    [
                        'group' => 'addGroup',
                        'options' => [
                            'resource' => 'deleteLocaleActionRoute',
                            'position' => ActionBuilderInterface::POSITION_TOP

                        ],
                    ],
                ],
                $this->expectedAddTwoActionDifferentGroupDifferentPosition
            ]
        ];
    }

    /**
     * Test add method
     *
     * @dataProvider addDataSource
     *
     * @param $actions
     * @param $expected
     */
    public function testAdd($actions, $expected)
    {
        foreach ($actions as $action) {
            $this->actionBuilder->add($action['group'], $action['options']);
        }

        $this->assertEquals($expected, $this->actionBuilder->getActions());
    }

    /**
     * Test add method with no specified position expect exception
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAdd_NoPosition_Exception()
    {
        $this->actionBuilder->add(
            'deleteGroup',
            [
                'resource' => 'deleteLocaleActionRoute',
            ]
        );
    }

    /**
     * Test add method with not valid position expect exception
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAdd_NotValidPosition_Exception()
    {
        $this->actionBuilder->add(
            'deleteGroup',
            [
                'resource' => 'deleteLocaleActionRoute',
                'position' => 'wrongPosition'
            ]
        );
    }
}
