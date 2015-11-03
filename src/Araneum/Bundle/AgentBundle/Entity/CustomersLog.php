<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CustomersLog
 *
 * @ORM\Table("araneum_customers_log")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\CustomersLogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CustomersLog
{
    use DateTrait;

    const STATUS_ERROR   = 0;
    const STATUS_SUCCESS = 101;

    private static $statusDescription =
        [
            self::STATUS_ERROR => 'Error',
            self::STATUS_SUCCESS => 'Success'
        ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Application
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Application")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    private $application;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\AgentBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255, nullable=true)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="spot_response", type="text", nullable=true)
     */
    private $spotResponse;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get application
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set application
     *
     * @param Application $application
     * @return CustomersLog
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get Customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set customer
     *
     * @param Customer $customer
     * @return CustomersLog
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get Action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return CustomersLog
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get Stop  Option response
     *
     * @return string
     */
    public function getSpotResponse()
    {
        return $this->spotResponse;
    }

    /**
     * Set spot Option response
     *
     * @param string $spotResponse
     * @return CustomersLog
     */
    public function setSpotResponse($spotResponse)
    {
        $this->spotResponse = $spotResponse;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return CustomersLog
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get description status
     *
     * @param $id
     * @return mixed
     */
    public function getStatusDescription($id)
    {
        return self::$statusDescription[$id];
    }
}